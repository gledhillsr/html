#!/bin/bash
#
# Put the Sample resort back the way it started.
#
# Sample is the resort anyone is invited to experiment in, so it fills up with other people's
# trial shifts, renamed templates and half-finished edits. SampleBackup is the pristine copy;
# this replaces Sample with it, and cron runs it at 2am.
#
#   SampleBackup  --->  Sample        (every night; Sample's contents are disposable)
#
# The direction never reverses. Nothing here writes to SampleBackup, so an experiment in Sample
# can never contaminate the master.
#
# **Schema changes have to reach SampleBackup too.** The restore copies SampleBackup's tables
# wholesale - their structure as well as their rows - so a column added to Sample alone would be
# undone at 2am. SampleBackup is in PatrolData.resortMap for exactly this reason: the site-wide
# jobs on Directors -> Site Wide Configuration walk every resort in that map, so it gets each
# schema change along with everybody else. It is listed there and nowhere else; it is not a resort
# anyone is meant to browse.
#
# Install (as ec2-user):
#   crontab -e
#   0 2 * * * /home/ec2-user/restoreSampleResort.sh >> /home/ec2-user/restoreSample.log 2>&1

set -euo pipefail

MASTER="SampleBackup"
LIVE="Sample"
CREDENTIALS="/etc/patrolCalendar/credentials.properties"

say() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"
}

die() {
  say "FAILED: $*"
  exit 1
}

[ -r "$CREDENTIALS" ] || die "cannot read $CREDENTIALS"

DB_USER=$(sed -n 's/^dbUser=//p' "$CREDENTIALS" | head -1)
DB_PASSWORD=$(sed -n 's/^dbPassword=//p' "$CREDENTIALS" | head -1)
[ -n "$DB_USER" ] && [ -n "$DB_PASSWORD" ] || die "dbUser/dbPassword missing from $CREDENTIALS"

# The password goes in a file readable only by us, never on a command line: anything passed as
# -p<password> is visible in `ps` to every user on the machine for as long as the command runs.
DEFAULTS=$(mktemp)
chmod 600 "$DEFAULTS"
trap 'rm -f "$DEFAULTS"' EXIT
cat > "$DEFAULTS" <<EOF
[client]
user=$DB_USER
password=$DB_PASSWORD
host=127.0.0.1
EOF

mysql_do()   { mysql --defaults-extra-file="$DEFAULTS" "$@"; }
mysqldump_do() { mysqldump --defaults-extra-file="$DEFAULTS" "$@"; }

say "restoring $LIVE from $MASTER"

# Refuse rather than guess. If the master is missing this would otherwise quietly install an empty
# Sample over a working one, and the damage is not obvious until somebody opens the demo.
mysql_do -N -e "SHOW DATABASES LIKE '$MASTER'" | grep -qx "$MASTER" \
  || die "$MASTER does not exist - create it first (see the note at the end of this script)"

MASTER_TABLES=$(mysql_do -N -e \
  "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$MASTER'")
[ "$MASTER_TABLES" -gt 0 ] || die "$MASTER has no tables; refusing to wipe $LIVE with nothing"
say "$MASTER holds $MASTER_TABLES table(s)"

mysql_do -e "CREATE DATABASE IF NOT EXISTS \`$LIVE\` DEFAULT CHARSET=utf8mb4"

# Dump to a file first, then load. Piping one into the other hides a dump failure behind mysql's
# exit code and can truncate Sample with a partial copy.
DUMP=$(mktemp)
trap 'rm -f "$DEFAULTS" "$DUMP"' EXIT
# MariaDB 10.5 on the server: no --set-gtid-purged, which is MySQL-only and aborts the dump.
mysqldump_do --add-drop-table --routines --triggers --events \
             --single-transaction "$MASTER" > "$DUMP" \
  || die "dump of $MASTER failed"
[ -s "$DUMP" ] || die "dump of $MASTER was empty"
say "dumped $(wc -c < "$DUMP") bytes"

# A table someone created in Sample that the master has never heard of would survive --add-drop-table,
# so those are cleared out first. Everything the master has is dropped and rebuilt by the dump.
EXTRA=$(mysql_do -N -e "
  SELECT table_name FROM information_schema.tables
   WHERE table_schema='$LIVE'
     AND table_name NOT IN (SELECT table_name FROM information_schema.tables
                             WHERE table_schema='$MASTER')")
if [ -n "$EXTRA" ]; then
  for table in $EXTRA; do
    say "dropping $LIVE.$table - not in $MASTER"
    mysql_do -e "DROP TABLE IF EXISTS \`$LIVE\`.\`$table\`"
  done
fi

mysql_do "$LIVE" < "$DUMP" || die "restoring $LIVE failed - it may be half-written, re-run this"

LIVE_TABLES=$(mysql_do -N -e \
  "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$LIVE'")
[ "$LIVE_TABLES" -eq "$MASTER_TABLES" ] \
  || die "$LIVE has $LIVE_TABLES tables but $MASTER has $MASTER_TABLES"

say "done: $LIVE restored from $MASTER, $LIVE_TABLES table(s)"

# ---------------------------------------------------------------------------
# Creating the master, once, from whatever Sample looks like today:
#
#   mysql  -e "CREATE DATABASE \`SampleBackup\` DEFAULT CHARSET=utf8mb4"
#   mysqldump --add-drop-table --routines --triggers --events Sample | mysql SampleBackup
#
# To re-baseline later - Sample has been improved and you want to keep it - run those same two
# lines again. That is the only supported way SampleBackup ever changes, apart from the site-wide
# schema jobs.
# ---------------------------------------------------------------------------

#!/bin/bash
# 404report.sh - what is 404ing on gledhills.com, and which of it matters.
#
# Run on the web server:  sudo bash 404report.sh [days]
#   days: how many of the retained logs to read, newest first. Default: all of them.
#
# Needs root: /var/log/nginx is drwx--x--x, so a normal user can cd into it but
# cannot list it - a bare /var/log/nginx/access.log* glob expands to nothing and
# every count silently comes back zero. That is why this refuses to run unprivileged.

set -uo pipefail

LOGDIR=/var/log/nginx
WEBROOT=/var/www/html
DAYS=${1:-0}

[ "$(id -u)" -eq 0 ] || { echo "must run as root (sudo bash $0)" >&2; exit 1; }
[ -d "$LOGDIR" ] || { echo "no $LOGDIR" >&2; exit 1; }

TMP=$(mktemp -d) && trap 'rm -rf "$TMP"' EXIT

# --- gather the logs, newest first, gz or not -------------------------------
ls -1t "$LOGDIR" | grep '^access\.log' > "$TMP/logs.txt"
[ "$DAYS" -gt 0 ] && head -n "$DAYS" "$TMP/logs.txt" > "$TMP/l" && mv "$TMP/l" "$TMP/logs.txt"

while read -r f; do
    case "$f" in
        *.gz) zcat "$LOGDIR/$f" ;;
        *)    cat  "$LOGDIR/$f" ;;
    esac
done < "$TMP/logs.txt" > "$TMP/all.log"

# Field 9 is $status and field 7 is the URI in the "main" log_format:
#   $remote_addr - $remote_user [$time_local] "$request" $status ...
# $request is one quoted field holding METHOD URI PROTO, so awk's default
# whitespace split puts the method at 6, the URI at 7 and the status at 9.
awk '$9==404' "$TMP/all.log" > "$TMP/404.log"

total=$(wc -l < "$TMP/all.log")
notfound=$(wc -l < "$TMP/404.log")

echo "=============================================================="
echo " 404 report - $(wc -l < "$TMP/logs.txt") log file(s)"
awk '{print $4}' "$TMP/all.log" | tr -d '[' | cut -d: -f1 | sort -u | tr '\n' ' '
echo; echo "=============================================================="
printf ' %8d requests\n %8d 404s (%d%%)\n' "$total" "$notfound" \
       "$(( total ? notfound * 100 / total : 0 ))"
echo

# --- 1. the raw top of the list ---------------------------------------------
echo "--- top 25 404 paths (mostly scanners; that is expected) ---"
awk '{print $7}' "$TMP/404.log" | sort | uniq -c | sort -rn | head -25
echo

# --- 2. case mismatches ------------------------------------------------------
# A real directory whose name has uppercase in it, requested in lowercase.
# Only real dirs count: the lowercase symlinks beside them are the fix, not the
# problem, and counting those as "mixed case" would hide every remaining miss.
find "$WEBROOT" -maxdepth 1 -type d ! -path "$WEBROOT" -printf '%f\n' 2>/dev/null | while read -r d; do
    l=$(printf '%s' "$d" | tr '[:upper:]' '[:lower:]')
    [ "$l" != "$d" ] && printf '%s %s\n' "$l" "$d"
done | sort > "$TMP/mixed.txt"

nlinks=$(find "$WEBROOT" -maxdepth 1 -type l 2>/dev/null | wc -l)
echo "--- case-mismatch 404s ---"
echo "    $(wc -l < "$TMP/mixed.txt") mixed-case dirs, $nlinks lowercase symlink(s) present"

awk '{print $7}' "$TMP/404.log" | awk -F/ '{print tolower($2)}' \
  | sort | uniq -c | sort -rn | sed 's/^ *//' > "$TMP/seg.txt"

# seg.txt is space-separated, NOT tab: splitting it on \t matches nothing and
# reports a cheerful zero. Cost me two wrong answers - leave the default FS alone.
awk 'NR==FNR { real[$1]=$2; next }
     ($2 in real) { printf "  %6d  /%s/...  (real dir: /%s/)\n", $1, $2, real[$2]; n++ }
     END { if (!n) print "  (none - every resort URL resolved)" }' \
    "$TMP/mixed.txt" "$TMP/seg.txt"
echo

# --- 3. the ones that are actual people --------------------------------------
# Strip the WordPress/PHP probe traffic. What is left is roughly what a person
# with a browser saw, and is the only part of a 76,000-line 404 count worth fixing.
PROBE='wp-|xmlrpc|wlwmanifest|\.env|\.php|phpmyadmin|/admin|/\.git|/\.well-known|autodiscover|owa/|cgi-bin'
echo "--- 404s that look like real visitors (probe traffic removed) ---"
grep -v -i -E "$PROBE" "$TMP/404.log" \
  | grep -v -i -E 'bot|spider|crawl|scan|curl|wget|python|go-http|zgrab' \
  | awk '{print $7}' | sort | uniq -c | sort -rn | head -25
echo

# --- 4. links your own pages are generating ----------------------------------
echo "--- 404s referred from gledhills.com (broken links in your own pages) ---"
awk -F'"' '$0 ~ / 404 / && $4 ~ /gledhills/' "$TMP/404.log" \
  | grep -v -i -E "$PROBE" \
  | awk -F'"' '{printf "  %s  <- %s\n", $2, $4}' | sort | uniq -c | sort -rn | head -20
echo "(blank = nothing on your site links to a missing page)"

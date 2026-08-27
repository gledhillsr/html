#!/bin/bash
# Give every title-case directory under /var/www/html an all-lowercase alias.
# Idempotent: skips anything that already exists. Run again after adding a resort.
set -euo pipefail
cd /var/www/html
created=0
for dir in */; do
    dir=${dir%/}
    lower=$(printf '%s' "$dir" | tr '[:upper:]' '[:lower:]')
    [ "$lower" = "$dir" ] && continue        # already lowercase
    [ -e "$lower" ] && continue              # a real directory, or the link is already there
    ln -s "$dir" "$lower"
    printf 'linked %s -> %s\n' "$lower" "$dir"
    created=$((created + 1))
done
printf '%d new alias(es)\n' "$created"

#!/bin/bash

LOCAL=$(dirname "$0")
LOG_FILE="/var/ossec/logs/active-responses.log"

read INPUT_JSON
FILENAME=$(echo "$INPUT_JSON" | sed -n 's/.*"syscheck":{"path":"\([^"]*\)".*/\1/p')
COMMAND=$(echo "$INPUT_JSON" | sed -n 's/.*"command":"\([^"]*\)".*/\1/p')

TIMESTAMP=$(date '+%Y/%m/%d %H:%M:%S')

if [ "$COMMAND" = "add" ]; then
    if [ -n "$FILENAME" ] && echo "$FILENAME" | grep -q "uploads/.*\.php"; then
        if [ -f "$FILENAME" ]; then
            QUARANTINE_DIR="/var/ossec/quarantine"
            mkdir -p "$QUARANTINE_DIR"
            BASENAME=$(basename "$FILENAME")
            mv "$FILENAME" "$QUARANTINE_DIR/${BASENAME}.$(date +%s).quarantined"
            echo "$TIMESTAMP active-response/bin/remove-webshell.sh: QUARANTINED $FILENAME" >> "$LOG_FILE"
        else
            echo "$TIMESTAMP active-response/bin/remove-webshell.sh: File not found $FILENAME" >> "$LOG_FILE"
        fi
    fi
elif [ "$COMMAND" = "delete" ]; then
    echo "$TIMESTAMP active-response/bin/remove-webshell.sh: delete command received (no rollback)" >> "$LOG_FILE"
fi

exit 0

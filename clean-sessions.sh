#!/bin/sh
#
# Clean media custom older than 4 days
#

find ./var/session -mtime +4 -print  -exec rm -f {} \;

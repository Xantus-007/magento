#!/bin/sh
#
# Clean media custom older than 4 days
#

find /data/www/monbento/production/prod/media/custom/ -mtime +4 \( -path /data/www/monbento/production/prod/media/custom/3380 -o -path /data/www/monbento/production/prod/media/custom/3929  \) -prune -o -print  -exec rm -f {} \;

#!/bin/bash

pg_dump --data-only --inserts -t map -t map_bonus -t map_box -t map_image -t map_image_file -U dba -W -h127.0.0.1 dba > ../api/var/sql/map.sql

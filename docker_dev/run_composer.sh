#!/bin/bash
docker exec -it siep-api-php chmod 777 ./storage -R
docker exec -it siep-api-php composer install


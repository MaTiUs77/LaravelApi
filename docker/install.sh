#!/bin/bash
docker exec -it laravel-api-php chmod 777 ./storage -R
docker exec -it laravel-api-php composer install --no-dev


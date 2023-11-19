#!/bin/bash

COMPOSER_MEMORY_LIMIT=-1 composer create-project symfony/skeleton symfony
mv  -fv ./symfony/* ./
rm -fR ./symfony
mkdir ./data
chmod 777 -R ./data
#!/usr/bin/env bash

SCRIPT=$( cd "$( dirname "$0" )" && pwd )/../public/index.php

php ${SCRIPT} load:assembly
php ${SCRIPT} load:party
php ${SCRIPT} load:constituency
php ${SCRIPT} load:committee
php ${SCRIPT} load:category

# php ${SCRIPT} load:congressman
# php ${SCRIPT} load:president

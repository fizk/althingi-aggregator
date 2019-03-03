#!/usr/bin/env bash

SCRIPT=$( cd "$( dirname "$0" )" && pwd )/../public/index.php

php ${SCRIPT} load:congressman --assembly=$1
php ${SCRIPT} load:plenary --assembly=$1
php ${SCRIPT} load:issue --assembly=$1
php ${SCRIPT} load:tmp-speech --assembly=$1
php ${SCRIPT} load:committee-assembly --assembly=$1
php ${SCRIPT} load:plenary-agenda --assembly=$1

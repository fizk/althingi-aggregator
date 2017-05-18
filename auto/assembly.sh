#!/usr/bin/env bash

#export AGGREGATE_NO_CACHE=AGGREGATE_NO_CACHE

SCRIPT=$( cd "$( dirname "$0" )" && pwd )/../../../public/index.php

#php $SCRIPT load:assembly
#php $SCRIPT load:party
#php $SCRIPT load:constituency
#php $SCRIPT load:committee
#php $SCRIPT load:category
php $SCRIPT load:congressman --assembly=$1
php $SCRIPT load:plenary --assembly=$1
php $SCRIPT load:issue --assembly=$1
php $SCRIPT load:committee-assembly --assembly=$1
#php $SCRIPT load:president

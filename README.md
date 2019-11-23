# AlthingiAggregator

This is a task-runner that will query data from https://www.althingi.is/altext/xml and send it over to
[Loggjafarthing](https://github.com/fizk/Loggjafarthing) for storing (if so configured).


## The bigger picture.
[Althingi.is](https://www.althingi.is/altext/xml) provides data in the form of XML documents. This service queried for these
documents, validates them and send them over to [Loggjafarthing](https://github.com/fizk/Loggjafarthing) for storing.

The process is split into **provide**, **extract** and **consume** and in theory, any part of this process can be
re-implemented to support different kinds of functionality.

The main purpose of this service to though to provide [Loggjafarthing](https://github.com/fizk/Loggjafarthing) with data
and therefor it is configured to read from [Althingi.is](https://www.althingi.is/altext/xml) and send the data
via HTTP to [Loggjafarthing](https://github.com/fizk/Loggjafarthing).

## Architecture.
Because [Althingi.is](https://www.althingi.is/altext/xml) doesn't provide any HTTP ETag, a request has to be made every time
data is required. This service might crash half way through its process and needs to be re-run, in which case we don't really
want to re-do all of these HTTP requests.

For this we have a **Provide** cache. If a request to [Althingi.is](https://www.althingi.is/altext/xml) was successful, let's cache
it for 12 hours or so. That way, we can run the service again up to the point it crashed the last time without bothering
[Althingi.is](https://www.althingi.is/altext/xml).

Likewise, if we need to re-run the service, if [Althingi.is](https://www.althingi.is/altext/xml) has already been provided
with this resource and has responded with a 2XX back and if nothing has changed since, then there is no need to bother it
again. For this we have the **Consumer** cache.

The code itself is written against an interface and doesn't care what type of cache is used. The service-manager on the other
hands does need to know. It provides two implementations: **memory** which resolves to Redis and **file** that will write
the cache to the filesystem as flat-files. The **file** one is good for development if you don't want any external dependencies,
the **memory** is the one that should be used in production.

This therefor needs the PHP-Redis extension to be available in PHP.

## Configure.
This service can be configured by providing the following environment variables.

| ENV                 | values                      | defaults    | description  |
| ------------------- |:----------------------------| ----------- | -------------|
| CONSUMER_CACHE_HOST        | <host name>          | localhost   |              |
| CONSUMER_CACHE_PORT        | <port number>        | 6379        |              |
| CONSUMER_CACHE_TYPE        | file / memory / none | none        |              |
| CONSUMER_CACHE             | true/false           | false       | Should the aggregator check if it has served this data to the API before and if so, not make a call to the API |
| PROVIDER_CACHE_HOST        | <host name>          | localhost   |              |
| PROVIDER_CACHE_PORT        | <port number>        | 6379        |              |
| PROVIDER_CACHE_TYPE        | file/memory/none     | none        |              |
| PROVIDER_CACHE             | true/false           | false       | Should the aggregator check if it has asked althingi.is for this information before and if so, use its cache and not make a HTTP request |
| AGGREGATOR_CONSUMER_SCHEMA | http / https         | http        |              |
| AGGREGATOR_CONSUMER_HOST   | <string>             | localhost   |              |
| AGGREGATOR_CONSUMER_PORT   | <string>             | 8080        |              |


## Tasks
This service runs tasks from the command-line. The syntax goes:
```shell script
$ cd ./public && php index.php [command]
```

| command                   | arguments                                         | description |
| ------------------------- | ------------------------------------------------- | ----------- |
| load:assembly             |                                                   |  |
| load:party                |                                                   |  |
| load:constituency         |                                                   |  |
| load:assembly:current     |                                                   |  |
| load:congressman          | --assembly= / -a                                  |  |
| load:minister             | --assembly= / -a                                  |  |
| load:ministry             |                                                   |  |
| load:plenary              | --assembly= / -a                                  |  |
| load:plenary-agenda       | --assembly= / -a                                  |  |
| load:issue                | --assembly= / -a                                  |  |
| load:single-issue         | --assembly= / -a  --issue= / -i  --category= / -c |  |
| load:committee            |                                                   |  |
| load:committee-assembly   | --assembly= / -a                                  |  |
| load:president            |                                                   |  |
| load:category             |                                                   |  |
| load:inflation            | --date= / -d                                      |  |
| load:government           |                                                   |  |
| load:tmp-speech           | --assembly= / -a                                  |  |

## Scripts
Because the commands often time need to be run in a specific order, there are bash scripts that can make tour live simpler.  
These script are located in the `./auto` directory.

* **globals.sh**
* **assembly.sh <number>**
* **issue.sh <number> <number> <string>**
* **presidents.sh**

**globals.sh**: Gets assemblies, parties, constituencies, committees and categories. All of these things
need to exist on the API's side before any other script is run. So make sure this one run first.

**assembly.sh <number>**: Gets everything related to on assembly: issues, speeches, congressmen... etc.
pass in as an argument the number of the assembly you want to process.

**issue.sh <number> <number> <string>**: Gets everything related to an issue.

**presidents.sh**: This one gets all congressmen as well as all presidents of the parliament.


## Docker
This repo comes with a Dockerfile that creates a PHP image that can run the service either for development or production.
This Dockerfile required optionally two build arguments

| argument     | values     | description |
| ------------ | ---------- | ----------- |
| WITH_XDEBUG  | true/false | Builds the image with/without xdebug support
| WITH_DEV     | true/false | Build the image with/without dev-dependencies (like PHPUnit)

## Docker Compose
This repo comes with a docker-compose.yml file that is used for development. It has twe services.

**run** will create an image that can run the scripts. It builds an image with Xdebug enabled and dev-dependencies.
The docker-compose file will set the _working_dir_ to `./auto` so you can run the scripts like so:
```shell script
$ docker-compose run run ./globals.sh
```


**test** Will create an image that is used for CI (Travis), it has dev-dependencies but not Xdebug to make it more prod-like.

You can pass environment variables in the docker-compose **run** service

* ENV_LOG_PATH
* ENV_CONSUMER_CACHE_TYPE
* ENV_CONSUMER_CACHE_HOST
* ENV_CONSUMER_CACHE_PORT
* ENV_CONSUMER_CACHE
* ENV_PROVIDER_CACHE_TYPE
* ENV_PROVIDER_CACHE_HOST
* ENV_PROVIDER_CACHE_PORT
* ENV_PROVIDER_CACHE
* ENV_AGGREGATOR_CONSUMER_SCHEMA
* ENV_AGGREGATOR_CONSUMER_HOST
* ENV_AGGREGATOR_CONSUMER_PORT

For PHPStorm
Create an interpreter: 
```shell script
docker-compose build run
```

This will create a Docker image that has the name **althingiaggregator_test** and has Xdebug installed.

Then in Settings | Languages and frameworks | PHP > CLI interpreter, 
pick the Docker image.

Next go over to   Settings | Languages and frameworks | PHP | Test Frameworks and select the same Docker image,
set this for the _path to script_ /opt/project/vendor/autoload.php

For default configuration file, set /opt/project/phpunit.xml.dist

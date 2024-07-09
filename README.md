# AlthingiAggregator

AlthingiAggregator is a command-line tool  for scraping https://www.althingi.is/altext/xml. It queries various endpoints and transforms the data before handing it off to another system for storing.

## Run for development
```sh
 docker compose -f docker-compose.yml -f docker-compose.local.yml up run
```

## Architecture.
Read [documentation here](https://einarvalur.co/blog/althingiaggregator)


## Requirements
This system requires:

* php: "^8.0",
* ext-dom
* ext-mbstring
* ext-json
* ext-redis

These requirements are installed in the `Dockerfile`.

## Configure.
This service can be configured by providing the following environment variables.

| ENV                        | values               | defaults    | description  |
| -------------------------- |:---------------------| ----------- | -------------|
| CONSUMER_CACHE_HOST        | &lt;host name&gt;    | localhost   |              |
| CONSUMER_CACHE_PORT        | &lt;port number&gt;  | 6379        |              |
| CONSUMER_CACHE_TYPE        | memory / none        | none        |              |
| PROVIDER_CACHE_HOST        | &lt;host name&gt;    | localhost   |              |
| PROVIDER_CACHE_PORT        | &lt;port number&gt;  | 6379        |              |
| PROVIDER_CACHE_TYPE        | memory / none        | none        |              |
| AGGREGATOR_CONSUMER_SCHEMA | http / https         | http        |              |
| AGGREGATOR_CONSUMER_HOST   | &lt;string&gt;       | localhost   |              |
| AGGREGATOR_CONSUMER_PORT   | &lt;string&gt;       | 8080        |              |


## Tasks
This service runs tasks from the command-line. The syntax goes:
```sh
$ php index.php [command]
```

The Dockerfile lands you in `/usr/src/bin` because that is where the scripts are (see below),
so you will need to `cd ../public` before you are able to run the commands

| command                           | arguments                                         | description  |
| --------------------------------- | --------------------------------------------------| ------------ |
| load:assembly                     |                                                   |              |
| load:party                        |                                                   |              |
| load:constituency                 |                                                   |              |
| load:assembly:current             |                                                   |              |
| load:congressman                  | --assembly={int}                                  |              |
| load:minister                     | --assembly={int}                                  |              |
| load:ministry                     |                                                   |              |
| load:parliamentary-session        | --assembly={int}                                  |              |
| load:parliamentary-session-agenda | --assembly={int}                                  |              |
| load:issue                        | --assembly={int}                                  |              |
| load:single-issue                 | --assembly={int}  --issue={int}  --category={A|B} |              |
| load:committee                    |                                                   |              |
| load:committee-assembly           | --assembly={int}                                  |              |
| load:president                    |                                                   |              |
| load:category                     |                                                   |              |
| load:inflation                    | --date={string}                                   |              |
| load:government                   |                                                   |              |
| load:tmp-speech                   | --assembly={int}                                  |              |

## Scripts
Because the commands, often time needs to be run in a specific order, there are bash scripts that can make your live simpler.
These script are located in the `./bin` directory. This directory is already in `$PATH`s

* **globals**
* **assembly &lt;number&gt;**
* **issue &lt;number&gt; &lt;number&gt; &lt;string&gt;**
* **members**

**globals**: Gets assemblies, parties, constituencies, committees and categories. All of these things
need to exist on the API's side before any other script is run. So make sure this one run first.

**assembly &lt;number&gt;**: Gets everything related to on an assembly: issues, speeches, congressmen... etc.
pass in as an argument the number of the assembly you want to process.

**issue &lt;number&gt; &lt;number&gt; &lt;string&gt;**: Gets everything related to an issue.

**members**: This one gets all congressmen as well as all presidents of the parliament.

To run these script in production/development:

```sh
$ docker compose run globals
$ docker compose run assembly 145
$ docker compose run issue 145 1 A
$ docker compose run members 145
```

## Development
To make development simpler, this repos comes with a `docker-compose.yml` file that defines two services:

### run
The service `docker compose run run`, builds a container off of the production `Dockerfile` in **development mode** and then maps all file that could change during development into the container as volumes. This includes the code, the config, the scripts and the **vendor** directory.

It also spins up an echo-server that acts as the recipient/consumer of outgoing messages from this service. This is just so that a separate recipient/consumer doesn't need to be spun up.

It also spins up and connect the Redis cache service.

To use this service simply run the required shell script like:

```sh
$ docker compose run globals
$ docker compose run assembly 145
```

This repo also comes with a `.env.example` file. To overwrite any configuration in the `docker-compose.yml` file, simply copy `.env.example` and rename it `.env`. Change any value you like. DockerCompose will pick this file up and pass any value you provided to the running docker container.

### test
The service `docker compose run test` runs PHPUnit and PHPCs before exiting. It also maps the code, the script and the tests as volumes into the container.

This allows for running all the test in development before deploying the service. This service is also used by the CI/CD service.

## Docker
This repo comes with a Dockerfile that creates a PHP image that can run the service either for development or production.
This Dockerfile required optionally a build argument

| argument     | values                   | description |
| ------------ | ------------------------ | ----------- |
| ENV          | production / development | Builds the image with/without xdebug support and/or **composer** dev dependencies

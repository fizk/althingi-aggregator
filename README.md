# AlthingiAggregator

althingi-aggregator is a command-line tool  for scraping https://www.althingi.is/altext/xml. It queries various endpoints and transforms the data before handing it off to another system for storing.


## Architecture.
At its core, this tool is split into three interfaces:

The **Providers**. Its job it to fetch XML files from Althingi repository and turn them into `DOMDocument` objects.

```php
namespace App\Provider;

use DOMDocument;

interface ProviderInterface
{
    public function get(string $url, callable $cb = null): DOMDocument;
}
```

The **Consumer**. Its job is to persist the data or hand it off to another systems for storing.
```php
namespace App\Consumer;

use App\Extractor\ExtractionInterface;
use DOMElement;

interface ConsumerInterface
{
    public function save(DOMElement $element, string $storageKey, ExtractionInterface $extract);
}
```

The **Extractor**. Its job is to accepts data from the **Providers**, transform, validate and reformat it before handing it off to the **Consumer** as a simple PHP `array`.
```php
namespace App\Extractor;

use DOMElement;

interface ExtractionInterface
{
    public function extract(DOMElement $object): array;
}
```

To glue these pieces together, this systems adopts the [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/) standard. Every command issued from the terminal is converted into a [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/) [`ServerRequestInterface`](https://www.php-fig.org/psr/psr-7/#321-psrhttpmessageserverrequestinterface) before handing off to a [PSR-15](https://www.php-fig.org/psr/psr-15/#21-psrhttpserverrequesthandlerinterface) `handler`.

To be able to pick the right handler, the system used a PSR-7 compatible Router. Each **Consumer/Provider** needs to be configured before use. Therefor this system has adopted the [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/) as a **ServiceManager** for [Dependency Injection](https://en.wikipedia.org/wiki/Dependency_injection).

Additional standards are adopted in the ServiceManager:

Rather that monitoring classes directly, the architecture calls for Events to be dispatched then the system completes a task or if an error occurs. To do this, the ServiceManager configures a [PSR-14: Event Dispatcher](https://www.php-fig.org/psr/psr-14/). This is mostly used for logging where a [PSR-3: Logger Interface](https://www.php-fig.org/psr/psr-3/) compatible library manages the logs.

If a task crashes half way through its process, it would be good to not have to re-run all successful queries. Althingi does not provide any cache-control headers, so our only option is to cache the results. The ServiceManager is configured with a [PSR-6: Caching Interface](https://www.php-fig.org/psr/psr-6/), and a [Redis](https://redis.io/) implementation. It is still up to the **Consumer/Provider** to use it.


The **Provider** queries the [altext/xml](https://www.althingi.is/altext/xml) HTTP service for data. The **Consumer** is expecting a HTTP REST API to accept the data. For this, there needs to be a HTTP client available. The ServiceManager provides a [PSR-18: HTTP Client](https://www.php-fig.org/psr/psr-18/) for all HTTP needs.


It is the goal of the architecture of this system to be configurable and expendable by adopting the following standards:

* [PSR-3: Logger Interface](https://www.php-fig.org/psr/psr-3/)
* [PSR-6: Caching Interface](https://www.php-fig.org/psr/psr-6/)
* [PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/)
* [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)
* [PSR-14: Event Dispatcher](https://www.php-fig.org/psr/psr-14/)
* [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)


## Requirements
This system requires:

* php: "^7.4",
* ext-dom
* ext-mbstring
* ext-json
* ext-redis


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
```s
$ php index.php [command]
```

The Dockerfile lands you in `/usr/src/bin` because there is where the scripts are (see below),
so you will need to `cd ../public` before you are able to run the commands

| command                   | arguments                             | description  |
| ------------------------- | --------------------------------------| ------------ |
| load:assembly             |                                                   |  |
| load:party                |                                                   |  |
| load:constituency         |                                                   |  |
| load:assembly:current     |                                                   |  |
| load:congressman          | --assembly={int}                                  |  |
| load:minister             | --assembly={int}                                  |  |
| load:ministry             |                                                   |  |
| load:plenary              | --assembly={int}                                  |  |
| load:plenary-agenda       | --assembly={int}                                  |  |
| load:issue                | --assembly={int}                                  |  |
| load:single-issue         | --assembly={int}  --issue={int}  --category={A|B} |  |
| load:committee            |                                                   |  |
| load:committee-assembly   | --assembly={int}                                  |  |
| load:president            |                                                   |  |
| load:category             |                                                   |  |
| load:inflation            | --date={string}                                   |  |
| load:government           |                                                   |  |
| load:tmp-speech           | --assembly={int}                                  |  |

## Scripts
Because the commands often time needs to be run in a specific order, there are bash scripts that can make your live simpler.
These script are located in the `./bin` directory.

* **globals.sh**
* **assembly.sh &lt;number&gt;**
* **issue.sh &lt;number&gt; &lt;number&gt; &lt;string&gt;**
* **presidents.sh**

**globals.sh**: Gets assemblies, parties, constituencies, committees and categories. All of these things
need to exist on the API's side before any other script is run. So make sure this one run first.

**assembly.sh &lt;number&gt;**: Gets everything related to on an assembly: issues, speeches, congressmen... etc.
pass in as an argument the number of the assembly you want to process.

**issue.sh &lt;number&gt; &lt;number&gt; &lt;string&gt;**: Gets everything related to an issue.

**presidents.sh**: This one gets all congressmen as well as all presidents of the parliament.


## Docker
This repo comes with a Dockerfile that creates a PHP image that can run the service either for development or production.
This Dockerfile required optionally a build argument

| argument     | values                   | description |
| ------------ | ------------------------ | ----------- |
| ENV          | production / development | Builds the image with/without xdebug support and/or **composer** dev dependencies

## Docker Compose
This repo comes with a docker-compose.yml file that is used for development. It has two services.

**run** will create an image that can run the scripts. It builds an image with Xdebug enabled and dev-dependencies.
The docker-compose file will set the _working_dir_ to `./bin` so you can run the scripts like so:
```shell script
$ docker-compose run run ./globals.sh
```

**test** Will create an image that is used for CI (Travis)

You can pass environment variables in the docker-compose **run** service

* ENV_CONSUMER_CACHE_TYPE
* ENV_CONSUMER_CACHE_HOST
* ENV_CONSUMER_CACHE_PORT
* ENV_PROVIDER_CACHE_TYPE
* ENV_PROVIDER_CACHE_HOST
* ENV_PROVIDER_CACHE_PORT
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


----

*2021-02-08*
The following repos are not PHP* ready, they have been temporarily forked and updated,
They should be pointed to the official repos, once they are updated.

* "phly/phly-event-dispatcher"
* "laminas/laminas-cache"
* "laminas/laminas-cache-storage-adapter-redis"
* "laminas/laminas-cache-storage-adapter-blackhole"

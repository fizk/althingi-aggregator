# AlthingiAggregator

Aggregator that collects data from `althingi.is/altext/xml/` and sends it to a REST service


This is not a web-application. It is meant to be run from the comment line.


![Place in the structure](https://cloud.githubusercontent.com/assets/386336/13869371/f8353370-ed27-11e5-919e-b2d3908d9b02.png)


## Configure
The application can be configured in the following way.

| ENV                 | values                                 | defaults              | description  |
| ------------------- |:---------------------------------------| --------------------- | -------------|
| CONSUMER_CACHE_HOST | <host name>                            | localhost             |              |
| CONSUMER_CACHE_PORT | <port number>                          | 6379                  |              |
| CONSUMER_CACHE_TYPE | file / memory / none                   | none                  |              |
| CONSUMER_CACHE      | true/false                             | false                 | Should the aggregator check if it has served this data to the API before and if so, not make a call to the API |
| PROVIDER_CACHE_HOST | <host name>                            | localhost             |              |
| PROVIDER_CACHE_PORT | <port number>                          | 6379                  |              |
| PROVIDER_CACHE_TYPE | file/memory/none                       | none                  |              |
| PROVIDER_CACHE      | true/false                             | false                 | Should the aggregator check if it has asked althingi.is for this information before and if so, use its cache and not make a HTTP request |
| LOGGER_SAVE         | true/false                             | false                 |              |
| LOGGER_STREAM       | true/false                             | false                 |              |
| LOGGER_FORMAT       | logstash / json / line / color / none  | none                  |              |
| AGGREGATOR_CONSUMER | <URL>                                  | http://localhost:8080 |              |


## Scripts

* **globals.sh**
* **assembly.sh <number>**
* **presidents.sh**

**globals.sh**: Gets assemblies, parties, constituencies, committees and categories. All of these things
need to exist on the API's side before any other script is run. So make sure this one run first.

**assembly.sh <number>**: Gets everything related to on assembly: issues, speeches, congressmen... etc.
pass in as an argument the number of the assembly you want to process.

**presidents.sh**: This one gets all congressmen as well as all presidents of the parliament.

## Docker

You can run the application from Docker as a single function/action. 
First you have to build it of course. Here I'm giving it the name of `aggregator`. The next thing is to actually run it. 

The Docker container will start in the `./auto` directory. From there you can run any of the **scripts**.

Since there are so many variables available, it's best to just load them from a file. 

```bash
$ docker build -t aggregator .

$ docker run -i --rm --env-file=./.env aggregator ./globals.sh
```

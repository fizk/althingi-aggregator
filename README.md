# AlthingiAggregator

Aggregator that collects data from `althingi.is/altext/xml/` and sends it to a REST service


This is not a web-application. It is meant to be run from the comment line.


![Place in the structure](https://cloud.githubusercontent.com/assets/386336/13869371/f8353370-ed27-11e5-919e-b2d3908d9b02.png)




| ENV                 | default                        | description  |
| ------------------- |:-------------------------------| -------------|
| CONSUMER_CACHE_HOST | localhost                      |              |
| CONSUMER_CACHE_PORT | 6379                           |              |
| CONSUMER_CACHE_TYPE | file/memory/none               |              |
| CONSUMER_CACHE      | true/false                     | Should the aggregator check if it has served this data to the API before and if so, not make a call to the API |
| PROVIDER_CACHE_HOST | localhost                      |              |
| PROVIDER_CACHE_PORT | 6379                           |              |
| PROVIDER_CACHE_TYPE | file/memory/none               |              |
| PROVIDER_CACHE      | true/false                     | Should the aggregator check if it has asked althingi.is for this information before and if so, use its cache and not make a HTTP request |
| LOGGER_SAVE         | true/false                     |              |
| LOGGER_STREAM       | true/false                     |              |
| LOGGER_FORMAT       | logstash/json/line/color/none  |              |
| AGGREGATOR_CONSUMER | http://localhost:8080          |              |

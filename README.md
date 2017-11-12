## Setup
* Copy `docker-compose.yml.dist` to `docker-compose.yml` 
* Run app, by running command `docker-compose up -d`
* Wait a little bit for docker to warm containers up (You might get "connection refused" error when trying to run initial script)

## Packages install & data sets loading
* Users. Run `docker-compose run users_php bash` and in opened console run command `sh app/Resources/docker/init.sh`
* Items. Run `docker-compose run items_php bash` and in opened console run command `sh app/Resources/docker/init.sh`
* Orders. Run `docker-compose run orders_php bash` and in opened console and in opened console run command `sh app/Resources/docker/init.sh`
* Gateway. Run `docker-compose run gateway_php bash` and in opened console and in opened console run command `sh app/Resources/docker/init.sh`

Gateway microservice is the main app running container that calls every other microservice to do system actions.
Gateway API documentation to know about existing methods and try methods in sandbox mode can be reached by URL `localhost:38080/api/doc`

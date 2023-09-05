DEPLOYMENT WITH DOCKER
------------
You'll want to copy the `.env.example` file to `.env` like usual. Run `composer install` to install dependencies and that should be it for the app.

Once you [install Docker](https://docs.docker.com/), you can start the containers using Docker Compose
```sh
$ docker-compose up -d
```

You should be able to visit the app at [http://localhost:9000](http://localhost:9000)
You should also be able to run artisan commands from your local machine :)

To stop the containers you can run `$ docker-compose kill`. If you'd like to remove them all together, after stopping run `$ docker-compose rm`.

`/deploy`
The deploy directory contains all of the docker configuration files (Dockerfiles, service config, etc).

`docker-compose.yml`
This file lets us tell Docker how to build our environment. When we call `docker-compose up` it will read this file and build the necessary containers as well as configure things like networking and volumes.

`deploy/app.docker`
This is the Dockerfile for our app container. It just extends the [PHP base image](https://hub.docker.com/_/php/) provided by Docker and installs some extra extensions that Laravel needs (mcrypt and mysql).

`deploy/web.docker`
This is the Dockerfile for our web container. It extends the [Nginx base image](https://hub.docker.com/_/nginx/) provided by Docker, and just adds an Nginx config file so our web service knows how to handle requests.

`deploy/vhost.conf`
This is the Nginx config file thats added to our web container. It's a pretty standard host configuration that proxy's PHP requests to our app container. You'll notice that it communicates with the app container via address `app:9000`. The `app` name is what we named our service and linked to in `docker-compose.yml`, so Docker will know we mean that container and route the request appropriately.

# TeamSpeak3-Server-Manager

## About

Application to run, handle and list TeamSpeak3 Server specific actions.


## Local Setup

### Start Docker Containers
```shell
docker-compose up -d
```

### Load Migrations
```shell
php bin/console doctrine:migrations:migrate --no-interaction
```

### Load Fixtures
```shell
php bin/console doctrine:fixtures:load --no-interaction
```

### Start PHP Dev-Server
```shell
php -S localhost:8080 -t public
```

### Visit In Browser
```
http://localhost:8080
```

## Reset Database

### Drop Database
```shell
php bin/console doctrine:database:drop --force
```

### Create Database
```shell
php bin/console doctrine:database:create
```

### Load Migrations
```shell
php bin/console doctrine:migrations:migrate --no-interaction
```

### Load Fixtures
```shell
php bin/console doctrine:fixtures:load --no-interaction
```
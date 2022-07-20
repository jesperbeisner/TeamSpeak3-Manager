# TeamSpeak3-Server-Manager

## About

Small application to set up TeamSpeak3 servers with docker and manage them via a web interface.

## Local Setup

```bash
# 1. Create sqlite database and load migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 2. Load test data
php bin/console app:create-test-data

# 3. Start local development server (Symfony CLI needs to be installed)
composer start

# 4. Visit
http://localhost:8080
```

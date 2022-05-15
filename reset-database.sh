# Only for local development sessions :)

php bin/console doctrine:database:drop --force --quiet
php bin/console doctrine:database:create --quiet

php bin/console doctrine:migrations:migrate --no-interaction --quiet
php bin/console doctrine:migrations:diff --allow-empty-diff --no-interaction --quiet
php bin/console doctrine:migrations:migrate --no-interaction --quiet

php bin/console doctrine:fixtures:load --no-interaction --quiet
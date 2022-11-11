release: php bin/console cache:clear && php bin/console cache:warmup && php bin/console doctrine:migrations:migrate --no-interaction
web: heroku-php-apache2 public/

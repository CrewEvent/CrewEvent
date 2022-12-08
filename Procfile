release: php bin/console cache:clear && php bin/console cache:warmup && php bin/console doctrine:migrations:migrate --no-interaction
web: heroku-php-apache2 public/
worker: * * * * * bin/console messenger:consume async --memory-limit=128M
#  && $env:MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!'; $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!';$env:SERVER_NAME='localhost:3014'; .\mercure\mercure.exe run -config .\mercure\Caddyfile.dev
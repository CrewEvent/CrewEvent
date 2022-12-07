
php bin/console cache:clear 
php bin/console cache:warmup 
php bin/console doctrine:migrations:migrate --no-interaction
bin/console doc:fix:load --no-interaction
exec apache2-foreground
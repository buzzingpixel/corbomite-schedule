@echo off

docker exec -it --user root --workdir /app php-corbomite-schedule bash -c "chmod +x /app/vendor/bin/phpunit && /app/vendor/bin/phpunit --configuration /app/phpunit.xml"

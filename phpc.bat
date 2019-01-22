@echo off

docker-compose up -d
docker exec -it --user root --workdir /app php-corbomite-schedule bash -c "php %*"

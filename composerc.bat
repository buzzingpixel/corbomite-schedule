@echo off

docker exec -it --user root --workdir /app php-corbomite-schedule bash -c "composer %*"

 cd ~/patrolCalendar/html/screenshots8
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.0-cli \
  sh -c "docker-php-ext-install mysqli >/dev/null && php -S 0.0.0.0:8000"

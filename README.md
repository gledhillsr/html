to get screenshots working, copy the files
screenshots_environment.ini to screenshots/environment.ini
screenshots_config.php to screenshots/config.php

sudo su
cd /var/www/html
git pull

## Get the code locally

First time on a machine (creates `~/patrolCalendar/html`):

```
mkdir -p ~/patrolCalendar
cd ~/patrolCalendar
git clone https://github.com/gledhillsr/html.git
cd html
```

Already cloned, to get the latest changes:

```
cd ~/patrolCalendar/html
git pull
```

## Run it locally

PHP and MySQL both run in Docker. Start Docker Desktop first.

### 1. Start the database

First time only. Use the `dbPassword` value from
`~/patrolCalendar/hiddenFiles/credentials.properties` as the password:

```
docker run --name patrol-calendar-mysql \
  -e MYSQL_ROOT_PASSWORD='<dbPassword>' \
  -e MYSQL_DATABASE=Brighton \
  -p 3306:3306 \
  -d mysql:8.0
```

Load the data. The dumps are not in git, so this assumes you have them in `~/Downloads`:

```
docker exec patrol-calendar-mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS Sample"'
docker exec -i patrol-calendar-mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" Brighton' < ~/Downloads/Brighton.sql
docker exec -i patrol-calendar-mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" Sample' < ~/Downloads/Sample.sql
```

After that, the container only needs starting:

```
docker start patrol-calendar-mysql
```

### 2. Add the database settings

First time only. `screenshots8/dbconfig.php` holds credentials and is not in git. Copy it from
`hiddenFiles`, then make sure `$mysqli_host` is `host.docker.internal`:

```
cp ~/patrolCalendar/hiddenFiles/html/screenshots8/dbconfig.php ~/patrolCalendar/html/screenshots8/dbconfig.php
```

### 3. Start the website <<<<<<  Start here, after the DB is running

```
cd ~/patrolCalendar/html
docker run --rm --name patrol-html -p 8000:8000 \
  -v "$PWD":/var/www/html -w /var/www/html php:8.0-cli \
  sh -c 'docker-php-ext-install mysqli && php -S 0.0.0.0:8000'
```

The first lines of output take a minute or so while the `mysqli` extension compiles. Then open:

- http://localhost:8000/ for the resort list
- http://localhost:8000/screenshots8/index.php for the Brighton screens

Press Ctrl-C to stop the website. The database keeps running until `docker stop patrol-calendar-mysql`.

Pages here change the local database, not production. The resort links redirect to
`/calendar-1/MonthCalendar`, which is the Java app in the separate `patrolCalendar1` repo, so they
will not load on port 8000. See that repo's README to run it on port 8080.

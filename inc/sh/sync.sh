#!/bin/bash

#docker-machine create --driver virtualbox superdock
#docker-machine start superdock
#docker-machine env superdock
#eval $(docker-machine env superdock)

unset WAY
WAY="$1"

unset ENV
ENV="$2"

unset SUPERDOCK_CORE_DIR
SUPERDOCK_CORE_DIR="$5"

unset SUPERDOCK_PROJECT_DIR
SUPERDOCK_PROJECT_DIR="$6"

case $WAY in

	from)
		unset DIST_DOMAIN
		DIST_DOMAIN="$3"
		unset LOCAL_DOMAIN
		LOCAL_DOMAIN="$2"
		echo "$(tput setaf 2)✔$(tput setaf 7) Executing task $(tput setaf 2)database:dist:dump$(tput setaf 7)"
		dep -f=$SUPERDOCK_CORE_DIR/dep/sync-from.php sync $ENV
		echo "$(tput setaf 2)✔$(tput setaf 7) Executing task $(tput setaf 2)database:dist:format$(tput setaf 7)"
		sed "s/$DIST_DOMAIN/$LOCAL_DOMAIN/g" $SUPERDOCK_PROJECT_DIR/superdock/database/${ENV}.sql > $SUPERDOCK_PROJECT_DIR/superdock/database/local.sql
		echo "$(tput setaf 2)✔$(tput setaf 7) Executing task $(tput setaf 2)database:dist:import$(tput setaf 7)"
		docker-compose -f $SUPERDOCK_CORE_DIR/docker/docker-compose.yml exec webserver sh -c "mysql --host=superdock_database --user=root --password=root db < /var/www/html/superdock/database/local.sql"
	;;

	to)
		unset LOCAL_DOMAIN
		LOCAL_DOMAIN="$3"
		unset DIST_DOMAIN
		DIST_DOMAIN="$4"
		echo "$(tput setaf 2)✔$(tput setaf 7) Executing task $(tput setaf 2)database:local:dump$(tput setaf 7)"
		docker-compose -f local/docker/docker-compose.yml exec webserver sh -c "mysqldump --host=superdock_database --user=root --password=root db > /var/www/html/local/db/local.sql"
		echo "$(tput setaf 2)✔$(tput setaf 7) Executing task $(tput setaf 2)database:local:format$(tput setaf 7)"
		sed "s/$LOCAL_DOMAIN/$DIST_DOMAIN/g" local/db/local.sql > local/db/${ENV}.dist.sql
		echo "$(tput setaf 2)✔$(tput setaf 7) Executing task $(tput setaf 2)database:local:export$(tput setaf 7)"
		dep -f=$SUPERDOCK_CORE_DIR/dep/sync-to.php sync $ENV
	;;

esac

osascript -e 'display notification "sync:ended" with title "superdock.local"'
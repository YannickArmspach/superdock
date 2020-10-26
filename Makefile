.PHONY: dev build install install-short install-dist install-dist-short

install: build install-default install-short

build:
	php ./build/phar.php

install-default:
	cp -rf dist/superdock.phar /usr/local/bin/superdock && chmod +x /usr/local/bin/superdock && superdock core install

install-short:
	cp -rf dist/superdock.phar /usr/local/bin/sd && chmod +x /usr/local/bin/sd && sd core install

install-dist:
	curl -LO https://github.com/YannickArmspach/superdock/raw/main/dist/superdock.phar && mv superdock.phar /usr/local/bin/superdock && chmod +x /usr/local/bin/superdock && superdock core install

install-dist-short:
	curl -LO https://github.com/YannickArmspach/superdock/raw/main/dist/superdock.phar && mv superdock.phar /usr/local/bin/sd && chmod +x /usr/local/bin/sd && sd core install

install-dev:
	#sudo sh -c -e "echo 'alias sd="php ~/superdock/bin/superdock.php"' >> ~/.bash_profile

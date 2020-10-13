.PHONY: dev build install install-short install-dist install-dist-short

dev: build install install-short

build:
	php ./build/phar.php

install:
	cp -rf dist/superdock.phar /usr/local/bin/superdock && chmod +x /usr/local/bin/superdock && superdock core install

install-short:
	cp -rf dist/superdock.phar /usr/local/bin/sd && chmod +x /usr/local/bin/sd && sd core install

install-dist:
	#curl -LO https://ynk.one/superdock/superdock.phar && mv superdock.phar /usr/local/bin/superdock && chmod +x /usr/local/bin/superdock && superdock core install

install-dist-short:
	#curl -LO https://ynk.one/superdock/superdock.phar && mv superdock.phar /usr/local/bin/sd && chmod +x /usr/local/bin/sd && sd core install


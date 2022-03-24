down:
	docker stop running-manager-apache
	docker rm running-manager-apache

dev-up:
	docker run -d --name=running-manager-apache -p 80:80 -v $(PWD)/manager:/app manager-apache

dev-build:
	docker build --file=manager/docker/development/php-cli.docker -t dev-php-cli manager/docker/development
	docker build --file=manager/docker/development/apache.docker -t manager-apache manager/docker/development

dev-cli:
	docker run --rm -v $(PWD)/manager:/app dev-php-cli php bin/app.php

prod-up:
	docker run -d --name=running-manager-apache -p 80:80 manager-apache

prod-build:
	docker build --file=manager/docker/production/php-cli.docker -t dev-php-cli manager
	docker build --file=manager/docker/production/apache.docker -t manager-apache manager

prod-cli:
	docker run --rm dev-php-cli php bin/app.php
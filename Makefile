down:
	docker stop nginx
	docker stop php-fpm
	docker rm nginx
	docker rm php-fpm
	docker network remove app

dev-up:
	docker network create app
	docker run -d --name=php-fpm -v $(PWD)/manager:/app --network=app php-fpm
	docker run -d --name=nginx -v $(PWD)/manager:/app -p 8080:80 --network=app nginx

dev-build:
	docker build --file=manager/docker/development/php-cli.docker -t php-cli manager/docker/development
	docker build --file=manager/docker/development/php-fpm.docker -t php-fpm manager/docker/development
	docker build --file=manager/docker/development/nginx.docker -t nginx manager/docker/development

dev-cli:
	docker run --rm -v $(PWD)/manager:/app dev-php-cli php bin/app.php

prod-up:
	docker network create app
	docker run -d --name=php-fpm --network=app php-fpm
	docker run -d --name=nginx -p 8080:80 --network=app nginx

prod-build:
	docker build --file=manager/docker/production/php-cli.docker -t php-cli manager
	docker build --file=manager/docker/production/php-fpm.docker -t php-fpm manager
	docker build --file=manager/docker/production/nginx.docker -t nginx manager

prod-cli:
	docker run --rm php-cli php bin/app.php
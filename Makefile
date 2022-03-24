REGISTRY := $(shell echo businesspunk)
TAG := $(shell echo 1.0)
USER_HOST := $(shell echo ec2-user@44.203.151.26)
PEM_KEY_PATH := $(shell echo /Users/nikitakazakevich/Downloads/Manager.pem)

up: dev-up
init: down pull dev-build up

down:
	docker-compose down --remove-orphans

pull:
	docker-compose pull

dev-up:
	docker-compose up -d

dev-build:
	docker-compose build

dev-cli:
	docker-compose run --rm php-cli php bin/app.php

prod-up:
	docker network create app
	docker run -d --name=php-fpm --network=app manager-php-fpm
	docker run -d --name=nginx -p 8080:80 --network=app manager-nginx

prod-build:
	docker build --pull --file=manager/docker/production/php-cli.docker -t $(REGISTRY)/manager-php-cli:$(TAG) manager
	docker build --pull --file=manager/docker/production/php-fpm.docker -t $(REGISTRY)/manager-php-fpm:$(TAG) manager
	docker build --pull --file=manager/docker/production/nginx.docker -t $(REGISTRY)/manager-nginx:$(TAG) manager

prod-cli:
	docker run --rm $(REGISTRY)/manager-php-cli:$(TAG) php bin/app.php

publish:
	docker push $(REGISTRY)/manager-php-cli:$(TAG)
	docker push $(REGISTRY)/manager-php-fpm:$(TAG)
	docker push $(REGISTRY)/manager-nginx:$(TAG)

deploy:
	ssh -i $(PEM_KEY_PATH) $(USER_HOST) 'rm -rf docker-compose.yml .env'
	scp -i $(PEM_KEY_PATH) docker-compose-production.yml $(USER_HOST):~/docker-compose.yml
	ssh -i $(PEM_KEY_PATH) $(USER_HOST) 'echo "REGISTRY=$(REGISTRY)" >> .env'
	ssh -i $(PEM_KEY_PATH) $(USER_HOST) 'echo "TAG=$(TAG)" >> .env'
	ssh -i $(PEM_KEY_PATH) $(USER_HOST) 'docker-compose up -d --build'
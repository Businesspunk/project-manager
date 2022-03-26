REGISTRY := $(shell echo businesspunk)
TAG := $(shell echo 3.0)
USER_HOST := $(shell echo ec2-user@44.203.151.26)
PEM_KEY_PATH := $(shell echo /Users/nikitakazakevich/Downloads/Manager.pem)

init: down pull dev-build manager-init up
up: dev-up
tests: manager-tests
down: 
	docker-compose down --remove-orphans

manager-init: manager-composer-install

manager-composer-install:
	docker-compose run --rm php-cli composer install

manager-tests:
	docker-compose run --rm php-cli php bin/phpunit

pull:
	docker-compose pull

dev-up:
	docker-compose up -d

dev-build:
	docker-compose build

prod-up:
	REGISTRY=$(REGISTRY) TAG=$(TAG) docker-compose -f docker-compose-production.yml up -d

prod-build:
	docker build --pull --file=manager/docker/production/php-cli.docker -t $(REGISTRY)/manager-php-cli:$(TAG) manager
	docker build --pull --file=manager/docker/production/php-fpm.docker -t $(REGISTRY)/manager-php-fpm:$(TAG) manager
	docker build --pull --file=manager/docker/production/nginx.docker -t $(REGISTRY)/manager-nginx:$(TAG) manager

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
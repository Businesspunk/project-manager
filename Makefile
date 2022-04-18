REGISTRY := $(shell echo businesspunk)
TAG := $(shell echo 3.3)
USER_HOST := $(shell echo ec2-user@44.203.151.26)
PEM_KEY_PATH := $(shell echo /Users/nikitakazakevich/Downloads/Manager.pem)
REDIS_SECRET := $(shell echo secret)

init: unready down pull dev-build up manager-init
up: dev-up
tests: manager-tests
down: 
	docker-compose down --remove-orphans

manager-init: manager-composer-install manager-assets-install ready manager-migrations manager-fixtures

ready:
	docker run --rm -v $(PWD)/manager:/app -w="/app" alpine touch .ready

unready:
	docker run --rm -v $(PWD)/manager:/app -w="/app" alpine rm -f .ready

manager-composer-install:
	docker-compose run --rm manager-php-cli composer install

manager-assets-install:
	docker-compose run --rm manager-node yarn install

manager-migrations:
	docker-compose run --rm manager-php-cli bin/console doctrine:migrations:migrate --no-interaction

manager-fixtures:
	docker-compose run --rm manager-php-cli bin/console doctrine:fixtures:load --no-interaction

manager-tests:
	docker-compose run --rm manager-php-cli php bin/phpunit

manager-wait-db:
	until docker-compose exec -T manager-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

pull:
	docker-compose pull

dev-up:
	docker-compose up -d

dev-build:
	docker-compose build

prod-up:
	REGISTRY=$(REGISTRY) TAG=$(TAG) docker-compose -f docker-compose-production.yml up -d

manager-migrations-prod:
	docker-compose run --rm $(REGISTRY)/manager-php-cli:$(TAG) bin/console doctrine:migrations:migrate --no-interaction

prod-build:
	docker build --pull --file=manager/docker/production/php-cli.docker -t $(REGISTRY)/manager-php-cli:$(TAG) manager
	docker build --pull --file=manager/docker/production/php-fpm.docker -t $(REGISTRY)/manager-php-fpm:$(TAG) manager
	docker build --pull --file=manager/docker/production/nginx.docker -t $(REGISTRY)/manager-nginx:$(TAG) manager

publish:
	docker push $(REGISTRY)/manager-php-cli:$(TAG)
	docker push $(REGISTRY)/manager-php-fpm:$(TAG)
	docker push $(REGISTRY)/manager-nginx:$(TAG)

deploy:
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) docker-compose-production.yml $(USER_HOST):~/docker-compose.yml
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'echo "REGISTRY=$(REGISTRY)" >> .env'
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'echo "TAG=$(TAG)" >> .env'
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'echo "REDIS_SECRET=$(REDIS_SECRET)" >> .env'
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'docker-compose up -d --build'
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'until docker-compose exec -T manager-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done'
	ssh -o StrictHostKeyChecking=no -i $(PEM_KEY_PATH) $(USER_HOST) 'docker-compose run --rm manager-php-cli bin/console doctrine:migrations:migrate --no-interaction'
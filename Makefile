#SHELL := /bin/bash
.PHONY: help

#.SILENT:

DOCKER_PHP=docker exec kravets-family_php_1 bash -c

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build:
	docker-compose build && docker-compose up
up:
	docker-compose up

php: ## enter php container
	docker exec -it kravets-family_php_1 bash

db: ## enter db container
	docker exec -it kravets-family_db_1 bash

clear: ## remove all cached data
	docker-compose stop && docker-compose rm -fv

symfony-cache:
	docker-compose exec php php /var/www/symfony/bin/console cache:clear

down:
	docker-compose down --remove-orphans

ps:
	docker ps

migrate:
	$(DOCKER_PHP) "php bin/console doctrine:migrations:migrate"

diff:
	$(DOCKER_PHP) "php bin/console doctrine:migrations:diff"

watch-front:
	npm run watch

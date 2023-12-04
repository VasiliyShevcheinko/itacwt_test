# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec $(TTY_OPTION) php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor

## —— 🎵 🐳 The Symfony-docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Home 🏠 ————————————————————————————————————————————————————————————————

install: ## Install project
	bash install.sh

start: ## Builds the Docker images
	@$(DOCKER_COMP) up -d --build

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

stop: ## Stop containers
	@$(DOCKER_COMP) stop

build-up: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

destroy: ## Stop the docker hub
	$(DOCKER_COMP) down --remove-orphans --volumes
	rm -rf volumes/ .env docker-compose.yml docker-compose.override.yml
	rm -rf src/backend/vendor src/backend/.env*.local

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=100 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

sh-consume: ## Connect to the PHP FPM container
	@$(PHP_CONT_CONSUME) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
install-symfony: vendor data-set tests ## Install symfony project

data-set:
	@#$(DOCKER_COMP) restart db && sleep 3
	@$(PHP_CONT) bin/db_recreate
	@$(PHP_CONT) bin/refresh_db
	@$(PHP_CONT) bin/initial_data-set

sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Очистка кэша Symfony
cc: sf

schema-update: ## Обновление схемы БД (+тестовой)
	@$(PHP_CONT) bin/refresh_db

.PHONY: install-symfony sf cc schema-update
## —— Symfony 🎵 Tests ———————————————————————————————————————————————————————————————
tests: ## Чистый запуск тестов (phpunit)
	@$(eval c ?=)
	@$(PHP_CONT) bin/run_tests $(c)

phpunit: ## Быстрый запуск phpunit
	@$(eval c ?=)
	@$(PHP_CONT) bin/phpunit $(c)

.PHONY: tests phpunit

.DEFAULT_GOAL := help
.PHONY: up down install db test.unit test.functional test.acceptance test test.clean test.coverage test.coverage \
php.metrics.open php.cs php.cbf php.md php.loc build health logs \
composer.update composer.install db.migrate db.seed dependences docker-cleanup help

DOCKER_C := docker-compose
APP_NAME := php
DOCK_X_APP := $(DOCKER_C) exec $(APP_NAME)

PHP_METRICS := /usr/local/lib/php-code-quality/vendor/bin/phpmetrics

VENDOR_PHINX := vendor/bin/phinx
VENDOR_CODECEPT := vendor/bin/codecept
VENDOR_PHPCS := ./vendor/squizlabs/php_codesniffer/bin/phpcs
VENDOR_PHPCBF := ./vendor/squizlabs/php_codesniffer/bin/phpcbf
VENDOR_METRICS := ./vendor/phpmetrics/phpmetrics/bin/phpmetrics
VENDOR_PHPMD := ./vendor/phpmd/phpmd/src/bin/phpmd

OUTPUT_COVERAGE := app/tests/_output/coverage/
OUTPUT_METRICS := app/tests/_output/php_code_quality/metrics_results/

up: ## Start docker container
	$(DOCKER_C) pull
	$(DOCKER_C) --env-file ./.env.dist up -d
# 	$(DOCKER_C) exec -T -e "WAIT_HOSTS=localhost:80" app /wait

down: ## Stop docker container
	$(DOCKER_C) down

test.unit: src/vendor ## Run unit tests suite
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run unit

test.functional: src/vendor ## Run functional tests suite
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run functional

test.api: src/vendor ## Run api tests suite
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run api

test.integration: src/vendor ## Run api tests suite
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run integration

test: src/vendor ## Run all available tests
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run

test.failed: src/vendor ## Run failed tests
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run -g failed

test.debug: src/vendor ## Debug all available tests
	$(DOCK_X_APP) php $(VENDOR_CODECEPT) run -vvv

test.unit-coverage: ## Check project test coverage
	$(DOCK_X_APP) php -dxdebug.mode=coverage $(VENDOR_CODECEPT) run unit --coverage --coverage-html --coverage-text
	open $(OUTPUT_COVERAGE)index.html >&- 2>&- || \
	xdg-open $(OUTPUT_COVERAGE)index.html >&- 2>&- || \
	gnome-open $(OUTPUT_COVERAGE)index.html >&- 2>&-

test.coverage: ## Check project test coverage
	$(DOCK_X_APP) php -dxdebug.mode=coverage $(VENDOR_CODECEPT) run --coverage --coverage-html --coverage-text
	open $(OUTPUT_COVERAGE)index.html >&- 2>&- || \
	xdg-open $(OUTPUT_COVERAGE)index.html >&- 2>&- || \
	gnome-open $(OUTPUT_COVERAGE)index.html >&- 2>&-

php.metrics: ## Run php metrics & open metrics web
	$(DOCKER_C) exec $(APP_NAME) php \
	$(VENDOR_METRICS) --excluded-dirs 'vendor','tests','db' \
	--report-html=./tests/_output/php_code_quality/metrics_results .
	make php.metrics.open

php.metrics.open:
	open $(OUTPUT_METRICS)index.html >&- 2>&- || \
	xdg-open $(OUTPUT_METRICS)index.html >&- 2>&- || \
	gnome-open $(OUTPUT_METRICS)index.html >&- 2>&-

php.cs: ## Run php code sniffer
	$(DOCK_X_APP) php $(VENDOR_PHPCS) \
	-sv --standard=PSR12 --extensions=php --ignore=vendor,tests,c3.php,db .

php.cbf: ## Run php Code Beautifier and Fixer
	$(DOCK_X_APP) php $(VENDOR_PHPCBF) \
	-sv --standard=PSR12 --extensions=php --ignore=vendor,tests,c3.php,db .

php.md: ## Run php mess detector
	$(DOCK_X_APP) php $(VENDOR_PHPMD) . \
	text cleancode,codesize,design,unusedcode --exclude 'vendor/*,tests/*,c3.php,db/*' --ignore-violations-on-exit

php.loc: ## Run php loc that analyzing the size and structure of the php project
	$(DOCK_X_APP) php \
	./vendor/phploc/phploc/phploc -v --names "*.php"  --exclude 'vendor','tests','db' .

build: ## Build docker image
	$(DOCKER_C) build

logs: ## Watch docker log files
	$(DOCKER_C) logs --tail 100 -f

composer.install:
	$(DOCK_X_APP) composer install

composer.update: ## Run composer update inside container
	$(DOCK_X_APP) composer update

composer.normalize: ## Run composer.json formater
	$(DOCK_X_APP) composer normalize

src/vendor:
	$(DOCK_X_APP) composer install

db.migrate:
	$(DOCKER_C) exec -T $(APP_NAME) $(VENDOR_PHINX) migrate

migration: ## Create phinx migration with argument name=MyNewMigration
	$(DOCKER_C) exec -T $(APP_NAME) $(VENDOR_PHINX) create $(name)


help:
	@grep -E '^[a-zA-Z._-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

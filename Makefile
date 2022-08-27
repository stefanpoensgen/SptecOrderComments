.DEFAULT_GOAL := help

## Variable definition
PLUGIN_ROOT=$(shell cd -P -- '$(shell dirname -- "$0")' && pwd -P)
PROJECT_ROOT=$(PLUGIN_ROOT)/../../..
SHOPWARE_ROOT=$(PLUGIN_ROOT)/../../../vendor/shopware

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

ecs-fix: ## Run easy coding standard on php
	@php $(PROJECT_ROOT)/vendor/bin/ecs check --fix --config=$(PROJECT_ROOT)/easy-coding-standard.php src bin tests
	@php $(PROJECT_ROOT)/vendor/bin/ecs check --fix src bin tests
.PHONY: ecs-fix

ecs-dry: ## Run easy coding style in dry mode
	@php $(PROJECT_ROOT)/vendor/bin/ecs check --config=$(PROJECT_ROOT)/easy-coding-standard.php src bin tests
	@php $(PROJECT_ROOT)/vendor/bin/ecs check src bin tests
.PHONY: ecs-dry

phpstan: ## Run phpstan
	@composer dump-autoload --dev
	@php $(PLUGIN_ROOT)/bin/phpstan-config-generator.php
	@php $(PROJECT_ROOT)/vendor/bin/phpstan analyze --configuration $(PLUGIN_ROOT)/phpstan.neon src tests
.PHONY: phpstan

phpunit:
	@composer dump-autoload --dev
	@touch $(PLUGIN_ROOT)/vendor/composer/InstalledVersions.php
	@$(PROJECT_ROOT)/vendor/bin/phpunit $(test)
.PHONY: phpunit

phpunit-coverage:
	make phpunit test="--coverage-html coverage $(test)"
.PHONY: phpunit

administration-fix: ## Run eslint on the administration files
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/eslint --ignore-path .eslintignore --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.eslintrc.js --ext .js,.vue --fix src/Resources/app/administration
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/stylelint --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.stylelintrc --fix src/Resources/app/administration/**/*.scss
.PHONY: administration-fix

storefront-fix: ## Run eslint on the storefront files
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/stylelint --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.stylelintrc --fix src/Resources/app/storefront/**/*.scss
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/eslint --ignore-path .eslintignore --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.eslintrc.js --ext .js,.vue --fix src/Resources/app/storefront
.PHONY: storefront-fix

administration-lint: ## Run eslint on the administration files
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/eslint --ignore-path .eslintignore --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.eslintrc.js --ext .js,.vue src/Resources/app/administration
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/stylelint --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.stylelintrc src/Resources/app/administration/**/*.scss
.PHONY: administration-lint

storefront-lint: ## Run eslint on the storefront files
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/stylelint --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.stylelintrc src/Resources/app/storefront/**/*.scss
	$(SHOPWARE_ROOT)/administration/Resources/app/administration/node_modules/.bin/eslint --ignore-path .eslintignore --config $(SHOPWARE_ROOT)/administration/Resources/app/administration/.eslintrc.js --ext .js,.vue src/Resources/app/storefront
.PHONY: storefront-lint

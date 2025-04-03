.PHONY: help
help: ## Show this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\n"}\
		/^[a-zA-Z_-]+:.*?##/\
		{printf "  \033[36m%-17s\033[0m %s\n", $$1, $$2}'\
		$(MAKEFILE_LIST)

.PHONY: qa-vendor-update
qa-vendor-update: ## Update vendors in the tools directory
	composer update --working-dir=tools/phpcs
	composer update --working-dir=tools/phpmd
	composer update --working-dir=tools/phpstan
	composer update --working-dir=tools/phpunit
	composer update --working-dir=tools/rector
	composer update --working-dir=tools/deptrac

.PHONY: qa-vendor-install
qa-vendor-install: ## Install vendors in the tools directory
	composer install --working-dir=tools/phpcs
	composer install --working-dir=tools/phpmd
	composer install --working-dir=tools/phpstan
	composer install --working-dir=tools/phpunit
	composer install --working-dir=tools/rector
	composer install --working-dir=tools/deptrac

.PHONY: qa-vendor-remove
qa-vendor-remove: ## Remove vendors in the tools directory
	rm -fr tools/phpcs/vendor
	rm -fr tools/phpmd/vendor
	rm -fr tools/phpstan/vendor
	rm -fr tools/phpunit/vendor
	rm -fr tools/rector/vendor
	rm -fr tools/deptrac/vendor

.PHONY: phpcs
phpcs: ## Run phpcs with dry run
	@echo "======== PHPCS ========"
	PHP_CS_FIXER_IGNORE_ENV=1 tools/phpcs/vendor/bin/php-cs-fixer fix --dry-run -v

.PHONY: phpmd
phpmd: ## Run phpmd
	@echo "======== PHPMD ========"
	tools/phpmd/vendor/bin/phpmd src/ text phpmd.xml
	tools/phpmd/vendor/bin/phpmd bin/ text phpmd.xml
	tools/phpmd/vendor/bin/phpmd tests/ text phpmd-tests.xml

.PHONY: phpstan
phpstan: ## Run phpstan analyse
	@echo "======== PHPSTAN ========"
	tools/phpstan/vendor/bin/phpstan analyse -v

.PHONY: phpunit
phpunit: ## Run phpunit
	@echo "======== PHPUNIT ========"
	tools/phpunit/vendor/bin/phpunit

.PHONY: phpunit-coverage
phpunit-coverage: ## Run phpunit coverage (/tmp/user_name/phpunit/coverage)
	@echo "======== PHPUNIT COVERAGE ========"
	XDEBUG_MODE=coverage tools/phpunit/vendor/bin/phpunit --coverage-html $$(dirname "$$(mktemp --dry-run)")/$$USER/phpunit/coverage

.PHONY: rector
rector: ## Run rector
	@echo "======== RECTOR ========"
	tools/rector/vendor/bin/rector process --dry-run

.PHONY: deptrac
deptrac: ## Run deptrac
	@echo "======== DEPTRAC ========"
	tools/deptrac/vendor/bin/deptrac

.PHONY: qa
qa: phpcs phpmd phpstan phpunit rector deptrac ## phpcs + phpmd + phpstan + phpunit + rector + deptrac

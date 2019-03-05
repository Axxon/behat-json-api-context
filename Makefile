check: cs stan ## (PHP) Launch all lint tools. A good choice for pre-commit hook

cs: vendor/bin ## (PHP) Code style checker
	@echo
	vendor/bin/php-cs-fixer fix -v --dry-run --allow-risky=yes --using-cache=no

fix: vendor/bin ## (PHP) Code style fixer
	@echo
	vendor/bin/php-cs-fixer fix --verbose --allow-risky=yes

security: vendor/bin ## (PHP) Check if application uses dependencies with known security vulnerabilities
	@echo
	bin/console security:check

stan: vendor/bin ## (PHP) Static analysis
	@echo
	vendor/bin/phpstan analyse -c phpstan.neon -l 7 src/

test: unit ## (PHP) Launch all test tools

unit: vendor/bin ## (PHP) Unit tests
	@echo
	vendor/bin/phpunit

behat: vendor/bin ## (PHP) Integration tests
	@echo
	vendor/bin/behat

vendor/bin:
	@echo
	composer install

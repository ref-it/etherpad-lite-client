test:
	./vendor/bin/phpunit --colors --verbose

dev-deps:
	composer install --dev

client:
	php ./tools/generate.php > ./etherpad-lite-client.php

.PHONY: all test clean

optimize:
	composer dump-autoload -o

test:
	./vendor/bin/phpunit

cs-fixer:
	./vendor/bin/php-cs-fixer fix ./src
	./vendor/bin/php-cs-fixer fix ./tests

analyse:
	./vendor/bin/phpstan analyse --level=max ./src ./tests

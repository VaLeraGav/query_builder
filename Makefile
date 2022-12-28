start:
	php bin/test.php

test:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit tests
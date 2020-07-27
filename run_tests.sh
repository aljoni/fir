#!/bin/bash
docker run -v $PWD:/app --rm phpunit/phpunit test/. --test-suffix=_test.php

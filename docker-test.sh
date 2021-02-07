#!/bin/sh

set -e

################################################################################

wait_for_db() {
	if [ "$WAIT_FOR_DB" = "false" ]; then
		return 0
	fi

	until nc -z -v -w60 "${DB_HOST:-db}" "${DB_PORT:-3306}"; do
		echo "Waiting for database connection..."
		# wait for 5 seconds before check again
		sleep 5
	done
}

################################################################################

wait_for_db

echo "Waiting to ensure everything is fully ready for the tests..."
sleep 15

if [ ! -d "$PROJECT_DIR" ]; then
	echo "No project to test found at '$PROJECT_DIR'!"
    exit 1
fi

cd "$PROJECT_DIR"

echo " "
echo "========"
echo "Install WP test deps..."
/install-wp-tests.sh;

echo " "
echo "========"
echo "Install plugin deps..."
composer install

echo " "
echo "========"
echo "Trigger PHPUnit tests..."
./vendor/bin/phpunit

echo " "
echo "========"
echo "Trigger PHPUnit tests with WP in multisite mode..."
WP_MULTISITE=1 ./vendor/bin/phpunit

echo " "
echo "========"
echo "Trigger PHP Code Sniffer (without warnings)..."
./vendor/bin/phpcs --warning-severity=0

echo " "
echo "========"
echo "Install node deps..."
npm install

echo " "
echo "========"
echo "Trigger ESLint.."
npx eslint .

echo " "
echo "========"
echo "Tests successful. Check logs for details."
return 0

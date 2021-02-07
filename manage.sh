#!/bin/bash
set -e

. .env

###########################################################
# Functions

log() {
    echo "[${0}] [$(date +%Y-%m-%dT%H:%M:%S)] $*"
}

prepare_release() {
    NEW_VERSION=${1}
    if [ -z "${NEW_VERSION}" ] ; then
        log 'Missing release version!'
        return 1;
    fi

    log 'TODO Updating app version...'
    sed -i \
        -e "s|\"version\": \".*\"|\"version\": \"${NEW_VERSION}\"|g" \
        ./.gitmoji-changelogrc

    log 'Updating plugin version...'
    sed -i \
        -e "s|version = '.*'|version = '${NEW_VERSION}'|g" \
        ./includes/"class-${WP_PLUGIN}.php"

    sed -i \
        -e "s|Version: .*|Version: ${NEW_VERSION}|g" \
        -e "s| __FILE__, '.*' | __FILE__, '${NEW_VERSION}' |g" \
        ./"${WP_PLUGIN}.php"

    # Generate Changelog for version
    log "Generating Changelog for version '${NEW_VERSION}'..."
    npm install
    npm run gitmoji-changelog

    # TODO Add and commit to git with message `:bookmark: Release X.Y.Z`

    ./bin/generate-plugin-zip.sh "${WP_PLUGIN}"
}

usage() {
    echo "usage: ./manage.sh COMMAND [ARGUMENTS]

    Commands:
        local-install       Install local env for WP tests and lint
        local-test          Execute tests in local env
        local-lint          Execute lint in local env
        local-i18n          Update i18n locales
        local-clean         Minify and clean source code
        local-prep-release  Prepare app release

        start               Start dev / test env (docker)
        stop                Stop dev / test env
        test                Start test env and verify plugin install correctly
        logs                Follow logs of dev / test env
        reset               Reset all data of dev / test env
        sut                 Execute commands in test container
        phpcbf              Execute PHP Code Beautifier and Fixer in test container
        wp                  Execute WP-CLI in WordPress container
    "
}

###########################################################
# Runtime

case "${1}" in
    # Local env
    local-install)
    composer install
    npm install
    ./bin/install-wp-tests.sh ${@:2};;

    local-test)
    ./vendor/bin/phpunit
    WP_MULTISITE=1 ./vendor/bin/phpunit;;

    local-lint)
    ./vendor/bin/phpcs --warning-severity=0
    npx eslint .;;

    local-i18n) npm run i18n;;
    local-clean)
    npm run start
    ./vendor/bin/phpcbf;;

    local-prep-release)
    npm run start
    prepare_release ${@:2};;

    # DEV env
    start) docker-compose up -d "${@:2}"
    chown 'www-data:www-data' -R '/srv/wordpress/html/wp-content';;
    stop) docker-compose down "${@:2}";;
    test) set -e
    docker-compose build
    docker-compose down -v
    docker-compose up -d
    docker-compose ps
    docker-compose logs -f sut
    docker-compose ps
    docker-compose logs wordpress
    docker-compose ps sut | grep -q 'Exit 0'
    docker-compose exec -T --user www-data wordpress wp core install --url="http://localhost" --title="WordPress-CI" --admin_user=admin --admin_password=password --admin_email=admin@yopmail.com
    docker-compose exec -T --user www-data wordpress wp plugin activate wp-plugin-template
    docker-compose down -v
    set +e;;
    logs) docker-compose logs -f "${@:2}";;
    reset) docker-compose down
    rm -rf /srv/wordpress;;
    sut) docker-compose run -T sut "${@:2}";;
    phpcbf) docker-compose run -T sut ./vendor/bin/phpcbf;;
    wp) docker-compose exec -T --user www-data wordpress wp "${@:2}";;
    i18n) npm install
    npm run i18n;;
    build) build;;
    prepare-release) build
    prepare_release "${@:2}";;
    # Help
    *) usage;;
esac

exit 0

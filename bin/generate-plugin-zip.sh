#!/usr/bin/env bash

zip -FSrq "${WP_PLUGIN:-${1}}" . -x "bin/*" "vendor/*" "node_modules/*" ".*" "*.md" "*.sh" "docker-compose*" "Dockerfile"


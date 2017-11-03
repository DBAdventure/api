#!/bin/bash

DIR=$(dirname $(readlink -f $0))
PROJECT_DIR="${DIR}/../"

## Source file requried for tasks
source ${DIR}/bash/common
source ${DIR}/bash/project

function execute() {
    COMMAND=$(echo "bin/${1}" $(echo ${@:2} | tr " " " "))
    display "Running command '${COMMAND}'"
    ${PROJECT_DIR}/$COMMAND
}

function static() {
    display "Running static tests"
    pushd ${PROJECT_DIR}
    execute "phpcs" "--colors --standard=PSR2 -p --extensions=php src/"
    execute "phpmd" "src/ text controversial,unusedcode --strict"
    check
    popd
}

function unit() {
    display "Running unit tests"
    pushd ${PROJECT_DIR}
    execute "phpunit"  "src/ ${@}"
    check
    popd
}


case "$1" in
    static)
        static
    ;;
    unit)
        unit "${@:2}"
    ;;
    *)
        static
        unit
    ;;
esac

display "Done!"

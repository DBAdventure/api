#!/bin/bash

DIR=$(dirname $(readlink -f $0))
PROJECT_DIR="${DIR}/../"

##Source file requried for tasks (Env is mandatory and must be first)
source ${DIR}/bash/env
source ${DIR}/bash/common
source ${DIR}/bash/project


function setup() {
    display "Install dependencies & setup \"${PLATFORM_ENV}\" environement"
    #the parmameter is the env to setup
    project_setup ${PLATFORM_ENV}
    check
}

function setup_cache() {
    display "Install dependencies & setup cache \"${PLATFORM_ENV}\" environement"
    #the parmameter is the env to setup
    cache_setup ${PLATFORM_ENV}
    check
}


case "$1" in
    cache)
        setup_cache
    ;;
    *)
        setup
    ;;
esac

display "Done!"

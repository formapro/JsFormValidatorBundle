#!/bin/bash

VERSION='~2.3.0'
DIR=$(pwd)
NATIVE="$DIR/Tests/TestBundles/DefaultTestBundle/Entity/CustomizationEntity.php"
SF23="$DIR/Tests/TestBundles/DefaultTestBundle/Entity/CustomizationEntity_sf_2_3.php"

if [[ "$VERSION" == "$1" ]]; then
    rm "$NATIVE"
    if [ -f "$NATIVE" ]; then
        echo "Native file is not deleted!"
        exit 1
    fi
    mv "$SF23" "$NATIVE"
    if [ ! -f "$NATIVE" ]; then
        echo "Mocked file is not moved!"
        exit 1
    fi
    echo 'Moved!'
fi
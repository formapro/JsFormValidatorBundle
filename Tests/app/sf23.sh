#!/bin/bash

VERSION='~2.3.0'
DIR=$(pwd)
if [[ "$VERSION" != "$1" ]]; then
    exit 0;
fi

move() {
    rm "$2"
    if [ -f "$2" ]; then
        echo "Native $2 file is not deleted"
        exit 1
    fi
    mv "$1" "$2"
    if [ ! -f "$2" ]; then
        echo "Mocked file $1 is not moved!"
        exit 1
    fi
    echo "$1 enabled!"
}

NATIVE="$DIR/Tests/TestBundles/DefaultTestBundle/Entity/CustomizationEntity.php"
SF23="$DIR/Tests/TestBundles/DefaultTestBundle/Entity/CustomizationEntity_sf_2_3.php"
move "$SF23" "$NATIVE"

NATIVE="$DIR/Tests/TestBundles/DefaultTestBundle/Entity/BasicConstraintsEntity.php"
SF23="$DIR/Tests/TestBundles/DefaultTestBundle/Entity/BasicConstraintsEntity_sf_2_3.php"
move "$SF23" "$NATIVE"
#!/bin/bash

# version bump a debianized git checkout
# usage: ./bump.sh <package> <version>
# usage example: ./bump.sh drupal-site-odt 20080925-1

PACKAGE=$1
VERSION=$2

if [ ! $PACKAGE ]
then
  echo "Sorry, please enter a package name (e.g drupal-site-odt)"
  exit 1
fi

if [ x$VERSION = x ]
then
  echo "Sorry, please give me a version. e.g 20080925-1"
  exit 1
fi

dch -v $VERSION
git tag $PACKAGE-$VERSION
git push origin tag $PACKAGE-$VERSION
git add debian/changelog
git commit
git push

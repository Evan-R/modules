#!/bin/sh

WHICHGIT=`which git`
GIT=`echo $WHICHGIT`

$GIT subsplit init git@github.com:seleneapp/modules.git


$GIT subsplit publish --no-tags Cache:git@github.com:seleneapp/cache.git
$GIT subsplit publish --no-tags Common:git@github.com:seleneapp/common.git
$GIT subsplit publish --no-tags Config:git@github.com:seleneapp/config.git
$GIT subsplit publish --no-tags Cryptography:git@github.com:seleneapp/cryptography.git
$GIT subsplit publish --no-tags DI:git@github.com:seleneapp/di.git
$GIT subsplit publish --no-tags Events:git@github.com:seleneapp/events.git
$GIT subsplit publish --no-tags Filesystem:git@github.com:seleneapp/filesystem.git
$GIT subsplit publish --no-tags TestSuite:git@github.com:seleneapp/testsuite.git
$GIT subsplit publish --no-tags Routing:git@github.com:seleneapp/routing.git
$GIT subsplit publish --no-tags Package:git@github.com:seleneapp/package.git
$GIT subsplit publish --no-tags Xml:git@github.com:seleneapp/xml.git
$GIT subsplit publish --no-tags Writer:git@github.com:seleneapp/writer.git

rm -rf ./.subsplit

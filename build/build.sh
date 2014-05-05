#!/bin/sh

WHICHGIT=`which git`
GIT=`echo $WHICHGIT`

$GIT subsplit init git@github.com:seleneapp/framework.git


$GIT subsplit publish --no-tags Selene/Components/Common:git@github.com:seleneapp/common.git
$GIT subsplit publish --no-tags Selene/Components/Cache:git@github.com:seleneapp/cache.git
$GIT subsplit publish --no-tags Selene/Components/Config:git@github.com:seleneapp/config.git
$GIT subsplit publish --no-tags Selene/Components/Kernel:git@github.com:seleneapp/kernel.git
$GIT subsplit publish --no-tags Selene/Components/Cryptography:git@github.com:seleneapp/cryptography.git
$GIT subsplit publish --no-tags Selene/Components/Events:git@github.com:seleneapp/events.git
$GIT subsplit publish --no-tags Selene/Components/Filesystem:git@github.com:seleneapp/filesystem.git
$GIT subsplit publish --no-tags Selene/Components/DI:git@github.com:seleneapp/dependency-injection.git
$GIT subsplit publish --no-tags Selene/Components/TestSuite:git@github.com:seleneapp/testsuite.git
$GIT subsplit publish --no-tags Selene/Components/Routing:git@github.com:seleneapp/routing.git
$GIT subsplit publish --no-tags Selene/Components/Package:git@github.com:seleneapp/package.git
$GIT subsplit publish --no-tags Selene/Components/Xml:git@github.com:seleneapp/xml.git

rm -rf ./.subsplit

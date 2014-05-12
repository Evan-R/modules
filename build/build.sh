#!/bin/sh

WHICHGIT=`which git`
GIT=`echo $WHICHGIT`

$GIT subsplit init git@github.com:seleneapp/components.git


$GIT subsplit publish --no-tags src/Selene/Components/Common:git@github.com:seleneapp/common.git
$GIT subsplit publish --no-tags src/Selene/Components/Cache:git@github.com:seleneapp/cache.git
$GIT subsplit publish --no-tags src/Selene/Components/Config:git@github.com:seleneapp/config.git
$GIT subsplit publish --no-tags src/Selene/Components/Kernel:git@github.com:seleneapp/kernel.git
$GIT subsplit publish --no-tags src/Selene/Components/Cryptography:git@github.com:seleneapp/cryptography.git
$GIT subsplit publish --no-tags src/Selene/Components/Events:git@github.com:seleneapp/events.git
$GIT subsplit publish --no-tags src/Selene/Components/Filesystem:git@github.com:seleneapp/filesystem.git
$GIT subsplit publish --no-tags src/Selene/Components/DI:git@github.com:seleneapp/di.git
$GIT subsplit publish --no-tags src/Selene/Components/TestSuite:git@github.com:seleneapp/testsuite.git
$GIT subsplit publish --no-tags src/Selene/Components/Routing:git@github.com:seleneapp/routing.git
$GIT subsplit publish --no-tags src/Selene/Components/Stack:git@github.com:seleneapp/stack.git
$GIT subsplit publish --no-tags src/Selene/Components/Package:git@github.com:seleneapp/package.git
$GIT subsplit publish --no-tags src/Selene/Components/Xml:git@github.com:seleneapp/xml.git

rm -rf ./.subsplit

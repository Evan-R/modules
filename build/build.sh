#!/bin/sh

WHICHGIT=`which git`
GIT=`echo $WHICHGIT`

$GIT subsplit init git@github.com:seleneapp/framework.git


$GIT subsplit publish --no-tags src/Selene/Components/Common:git@github.com:seleneapp/common.git
$GIT subsplit publish --no-tags src/Selene/Components/Events:git@github.com:seleneapp/events.git
$GIT subsplit publish --no-tags src/Selene/Components/Filesystem:git@github.com:seleneapp/filesystem.git
$GIT subsplit publish --no-tags src/Selene/Components/DependencyInjection:git@github.com:seleneapp/dependency-injection.git
$GIT subsplit publish --no-tags src/Selene/Components/TestSuite:git@github.com:seleneapp/testsuite.git

rm -rf ./.subsplit

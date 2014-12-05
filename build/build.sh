#!/bin/sh

WHICHGIT=`which git`
GIT=`echo $WHICHGIT`

$GIT subsplit init git@github.com:lucidphp/lucid.git

$GIT subsplit publish --no-tags src/Lucid/Module/Cache:git@github.com:lucidphp/cache.git
$GIT subsplit publish --no-tags src/Lucid/Module/Common:git@github.com:lucidphp/common.git
$GIT subsplit publish --no-tags src/Lucid/Module/Event:git@github.com:lucidphp/event.git
$GIT subsplit publish --no-tags src/Lucid/Module/Routing:git@github.com:lucidphp/routing.git
$GIT subsplit publish --no-tags src/Lucid/Module/Template:git@github.com:lucidphp/template.git

rm -rf ./.subsplit

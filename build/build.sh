#!/bin/sh

# clean up subsplit files
cleanUp() {
	rm -rf ./.subsplit
}

# abort if error and clean up subsplit files
abortIf() {

	if [ $1 != 0 ]; then
		cleanUp
		echo $2
		exit $1
	fi

	return 0
}

GIT=`which git`
SUBSPLIT=`$GIT subsplit init git@github.com:lucidphp/lucid.git`
echo $?
abortIf $? $SUBSPLIT

DIRS=`ls -d lucid/*`

for SUBREPO in $DIRS; do
	IFS='/' read -r -a NAME <<< "$SUBREPO"
	echo "publish ${NAME[1]}"
	RET=`$GIT subsplit publish --no-tags $SUBREPO:git@github.com:lucidphp/${NAME[1]}.git`
	abortIf $? $RET
done



cleanUp
exit 0

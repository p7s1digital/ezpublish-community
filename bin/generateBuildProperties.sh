#!/bin/bash

GIT_REVISION=`git rev-parse HEAD`
GIT_REVISION_SHORT=`git rev-parse --short HEAD`

echo -e "GIT_REVISION=$GIT_REVISION\nGIT_REVISION_SHORT=$GIT_REVISION_SHORT" > build.properties
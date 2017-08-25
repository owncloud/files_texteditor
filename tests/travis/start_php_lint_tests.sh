#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis
# @copyright 2017 Phillip Davis phil@jankaritech.com
#
cd apps/files_texteditor
echo "Running PHP lint tests"
find . -name \*.php -not -path './vendor/*' -exec php -l "{}" \;

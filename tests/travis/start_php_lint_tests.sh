#!/bin/bash
#
# ownCloud
#
# @author Phil Davis
# @copyright 2017 Phil Davis phil@jankaritech.com
#
cd apps/files_texteditor
echo "Running PHP lint tests"
find . -name \*.php -not -path './vendor/*' -exec php -l "{}" \;

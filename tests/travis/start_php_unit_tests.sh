#!/bin/bash
#
# ownCloud
#
# @author Phil Davis
# @copyright 2017 Phil Davis phil@jankaritech.com
#
cd apps/files_texteditor/tests
echo "Running PHP unit tests"
phpunit --configuration phpunit.xml

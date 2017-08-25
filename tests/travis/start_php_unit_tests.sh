#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis
# @copyright 2017 Phillip Davis phil@jankaritech.com
#
cd apps/files_texteditor/tests
echo "Running PHP unit tests"
phpunit --configuration phpunit.xml

#!/bin/bash
#
# ownCloud
#
# @author Phil Davis
# @copyright 2017 Phil Davis phil@jankaritech.com
#
cd apps/files_texteditor/tests
echo "Create coverage report"
wget https://scrutinizer-ci.com/ocular.phar
php ocular.phar code-coverage:upload --format=php-clover clover.xml

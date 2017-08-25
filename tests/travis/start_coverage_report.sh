#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis <phil@jankaritech.com>
# @copyright 2017 Phillip Davis phil@jankaritech.com
#
cd apps/files_texteditor/tests
echo "Create coverage report"
wget https://scrutinizer-ci.com/ocular.phar
php ocular.phar code-coverage:upload --format=php-clover clover.xml

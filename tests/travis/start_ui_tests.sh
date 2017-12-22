#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis <phil@jankaritech.com>
# @copyright 2017 Phillip Davis phil@jankaritech.com
#
echo "Running UI tests"
export APPS_TO_ENABLE="files_texteditor"
bash tests/travis/start_ui_tests.sh --config apps/files_texteditor/tests/ui/config/behat.yml

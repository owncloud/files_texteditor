#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis <phil@jankaritech.com>
# @copyright 2017 Phillip Davis phil@jankaritech.com
#
echo "Running UI tests"
export APPS_TO_ENABLE="files_texteditor"
pushd tests/acceptance
./run.sh --config ../../apps/files_texteditor/tests/acceptance/config/behat.yml --suite webUITextEditor
ACCEPTANCE_TEST_EXIT_STATUS=$?
popd
exit $ACCEPTANCE_TEST_EXIT_STATUS

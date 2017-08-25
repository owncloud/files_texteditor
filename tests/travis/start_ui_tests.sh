#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis
# @copyright 2017 Phillip Davis phil@jankaritech.com
#
cp tests/ui/features/bootstrap/FeatureContext.php apps/files_texteditor/tests/ui/features/bootstrap/FeatureContext.php
echo "Running UI tests"
bash tests/travis/start_behat_tests.sh --config apps/files_texteditor/tests/ui/config/behat.yml

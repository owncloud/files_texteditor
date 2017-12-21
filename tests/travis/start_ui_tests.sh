#!/bin/bash
#
# ownCloud
#
# @author Phillip Davis <phil@jankaritech.com>
# @copyright 2017 Phillip Davis phil@jankaritech.com
#

#@param $1 admin password
#@param $2 occ url
#@param $3 command
#sets $REMOTE_OCC_STDOUT and $REMOTE_OCC_STDERR from returned xml date
#@return occ return code given in the xml data
remote_occ() {
	RESULT=`curl -s -u admin:$1 $2 -d "command=$3"`
	RETURN=`echo $RESULT | xmllint --xpath "string(ocs/data/code)" - | sed 's/ //g'`
	#we could not find a proper return of the testing app, so something went wrong
	if [ -z "$RETURN" ]
	then
		RETURN=1
		REMOTE_OCC_STDERR=$RESULT
	else
		REMOTE_OCC_STDOUT=`echo $RESULT | xmllint --xpath "string(ocs/data/stdOut)" - | sed 's/ //g'`
		REMOTE_OCC_STDERR=`echo $RESULT | xmllint --xpath "string(ocs/data/stdErr)" - | sed 's/ //g'`
	fi
	return $RETURN
}


BASE_URL="http://$SRV_HOST_NAME"
if [ ! -z "$SRV_HOST_PORT" ] && [ "$SRV_HOST_PORT" != "80" ]
then
	BASE_URL="$BASE_URL:$SRV_HOST_PORT"
fi

if [ -n "$SRV_HOST_URL" ]
then
	BASE_URL="$BASE_URL/$SRV_HOST_URL"
fi

OCC_URL="$BASE_URL/ocs/v2.php/apps/testing/api/v1/occ"
if [ -z "$ADMIN_PASSWORD" ]
then
	ADMIN_PASSWORD="admin"
fi

remote_occ $ADMIN_PASSWORD $OCC_URL "--no-warnings app:list ^files_texteditor"
PREVIOUS_FILES_TEXTEDITOR_APP_STATUS=$REMOTE_OCC_STDOUT

if [[ "$PREVIOUS_FILES_TEXTEDITOR_APP_STATUS" =~ ^Disabled: ]]
then
	FILES_TEXTEDITOR_APP_ENABLED_BY_SCRIPT=true;
	remote_occ $ADMIN_PASSWORD $OCC_URL "--no-warnings app:enable files_texteditor"
else
	FILES_TEXTEDITOR_APP_ENABLED_BY_SCRIPT=false;
fi

echo "Running UI tests"
bash tests/travis/start_ui_tests.sh --config apps/files_texteditor/tests/ui/config/behat.yml

if test "$FILES_TEXTEDITOR_APP_ENABLED_BY_SCRIPT" = true; then
	remote_occ $ADMIN_PASSWORD $OCC_URL "--no-warnings app:disable files_texteditor"
fi

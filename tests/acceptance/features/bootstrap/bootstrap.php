<?php
require __DIR__ . '/../../../../../../lib/composer/autoload.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/bootstrap/WebUIBasicStructure.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/bootstrap/WebUIGeneralContext.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/bootstrap/WebUIFilesContext.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/lib/OwncloudPage.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("Page\\", __DIR__ . "/../lib", true);
$classLoader->addPsr4(
	"Page\\", __DIR__ . "/../../../../../../tests/acceptance/features/lib/", true
);
$classLoader->addPsr4(
	"TestHelpers\\", __DIR__ . "/../../../../../../tests/TestHelpers/", true
);
$classLoader->addPsr4(
	"TestHelpers\\Files_TextEditor\\", __DIR__ . "/../../../TestHelpers/", true
);
$classLoader->register();

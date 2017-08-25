<?php
require __DIR__ . '/../../../../../../lib/composer/autoload.php';
require __DIR__ . '/../../../../../../tests/ui/features/bootstrap/BasicStructure.php';
require __DIR__ . '/../../../../../../tests/ui/features/bootstrap/FeatureContext.php';
require __DIR__ . '/../../../../../../tests/ui/features/bootstrap/FilesContext.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("Page\\", __DIR__ . "/../lib", true);
$classLoader->addPsr4(
	"Page\\", __DIR__ . "/../../../../../../tests/ui/features/lib/", true
);
$classLoader->addPsr4(
	"TestHelpers\\", __DIR__ . "/../../../../../../tests/TestHelpers/", true
);
$classLoader->addPsr4(
	"TestHelpers\\Files_TextEditor\\", __DIR__ . "/../../../TestHelpers/", true
);
$classLoader->register();

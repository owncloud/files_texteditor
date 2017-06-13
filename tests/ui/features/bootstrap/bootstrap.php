<?php
require __DIR__ . '/../../../../../../lib/composer/autoload.php';
require __DIR__. '/../../../../../../tests/ui/features/bootstrap/BasicStructure.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("Page\\", __DIR__. "/../lib", true);
$classLoader->addPsr4("Page\\", __DIR__. "/../../../../../../tests/ui/features/lib/", true);
$classLoader->register();

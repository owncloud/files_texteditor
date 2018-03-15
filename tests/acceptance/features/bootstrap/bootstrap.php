<?php
/**
 * ownCloud
 *
 * @author Phillip Davis <phil@jankaritech.com>
 * @copyright 2017 Phillip Davis phil@jankaritech.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require __DIR__ . '/../../../../../../lib/composer/autoload.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/bootstrap/WebUIBasicStructure.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/bootstrap/WebUIGeneralContext.php';
require __DIR__ . '/../../../../../../tests/acceptance/features/bootstrap/WebUILoginContext.php';
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

<?php
/**
 * ownCloud
 *
 * @author Phillip Davis <phil@jankaritech.com>
 * @copyright Copyright (c) 2026, ownCloud GmbH
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License,
 * as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Cjm\Behat\StepThroughExtension\ServiceContainer\StepThroughExtension;
use rdx\behatvars\BehatVariablesExtension;

$featureContextArgs = [
	'baseUrl' => 'http://localhost:8080',
	'adminUsername' => 'admin',
	'adminPassword' => 'admin',
	'regularUserPassword' => 123456,
	'ocPath' => 'apps/testing/api/v1/occ',
];

return (new Config())
	->withProfile(
		(new Profile(
			'default',
			[
			'autoload' => [
			'' => '%paths.base%/../features/bootstrap',
			],
			]
		))
			->withExtension(new Extension(StepThroughExtension::class))
			->withExtension(new Extension(BehatVariablesExtension::class))
			->withSuite(
				(new Suite('webUITextEditor'))
					->addContext('TextEditorContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIFilesContext')
					->addContext('WebUISharingContext')
					->addContext('OccContext')
					->addContext('TrashbinContext')
					->withPaths('%paths.base%/../features/webUITextEditor')
			)
			->withSuite(
				(new Suite('webUIActivityList'))
					->addContext('TextEditorContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIFilesContext')
					->addContext('WebUIActivityContext')
					->withPaths('%paths.base%/../features/webUIActivityList')
			)
	);

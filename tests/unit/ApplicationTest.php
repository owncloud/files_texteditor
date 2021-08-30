<?php
/**
 * ownCloud
 *
 * @author Jannik Stehle <jstehle@owncloud.com>
 * @author Jan Ackermann <jackermann@owncloud.com>
 * @copyright (C) 2021 ownCloud GmbH
 * @license ownCloud Commercial License
 *
 * This code is covered by the ownCloud Commercial License.
 *
 * You should have received a copy of the ownCloud Commercial License
 * along with this program. If not, see
 * <https://owncloud.com/licenses/owncloud-commercial/>.
 *
 */

namespace OCA\Files_Texteditor\Tests\Unit;

use OCA\Files_Texteditor\AppInfo\Application;
use Test\TestCase;

/**
 * Class ApplicationTest
 *
 * Integration test to test the app framework initialisation
 *
 * @group DB
 *
 * @package OCA\Files_Backup\Tests\Unit
 */
class ApplicationTest extends TestCase {
	/**
	 * @var Application
	 */
	private $application;

	public function setUp(): void {
		$this->application = new Application();
	}

	public function testRegisterEventHooks() {
		$this->assertNull(static::invokePrivate($this->application, 'registerEventHooks'));
	}

	public function testBoot() {
		$this->assertNull($this->application->boot());
	}
}

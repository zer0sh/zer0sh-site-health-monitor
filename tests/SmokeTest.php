<?php
declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Tests;

use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase {

	public function test_composer_autoload_loaded(): void {
		$this->assertTrue( class_exists( 'Composer\\InstalledVersions' ) );
	}

	public function test_zer0sh_shm_abspath_defined(): void {
		$this->assertTrue( defined( 'ABSPATH' ) );
	}
}
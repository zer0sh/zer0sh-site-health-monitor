<?php
declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Tests;

use PHPUnit\Framework\TestCase;
use Zer0sh\SiteHealthMonitor\Checks\PhpExtensionsCheck;
use Zer0sh\SiteHealthMonitor\Result;

final class PhpExtensionsCheckTest extends TestCase {

	public function test_todas_las_extensiones_presentes(): void {
		$check = new PhpExtensionsCheck(
			static function ( string $extension ): bool {
				unset( $extension );
				return true;
			}
		);

		$result = $check->run();

		$this->assertTrue( $result->is_ok() );
		$this->assertSame( 'php/extensions', $result->get_id() );
	}

	public function test_extensiones_faltantes_se_reportan(): void {
		$check = new PhpExtensionsCheck(
			static function ( string $extension ): bool {
				return 'curl' !== $extension && 'mbstring' !== $extension;
			}
		);

		$result = $check->run();

		$this->assertSame( Result::SEVERITY_WARNING, $result->get_severity() );
		$this->assertSame( 5, $result->get_points_deducted() );
		$this->assertContains( 'curl', $result->get_value() );
		$this->assertContains( 'mbstring', $result->get_value() );
	}
}
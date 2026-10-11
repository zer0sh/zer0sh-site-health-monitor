<?php
declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Tests\Checks;

use Zer0sh\SiteHealthMonitor\Checks\AbstractCheck;
use Zer0sh\SiteHealthMonitor\Result;

final class BrokenCheck extends AbstractCheck {

	public function get_id(): string {
		return 'test/broken';
	}

	public function get_title(): string {
		return 'Check que falla';
	}

	public function run(): Result {
		throw new \RuntimeException( 'Fallo simulado' );
	}
}
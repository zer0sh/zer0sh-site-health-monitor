<?php
declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Tests\Checks;

use Zer0sh\SiteHealthMonitor\Checks\AbstractCheck;
use Zer0sh\SiteHealthMonitor\Checks\MultiCheckInterface;
use Zer0sh\SiteHealthMonitor\Result;

final class BrokenMultiCheck extends AbstractCheck implements MultiCheckInterface {

	public function get_id(): string {
		return 'test/broken_multi';
	}

	public function get_title(): string {
		return 'Check multi que falla';
	}

	public function run(): Result {
		return $this->result( Result::SEVERITY_OK, null );
	}

	public function run_many(): array {
		throw new \RuntimeException( 'Fallo multi simulado' );
	}
}
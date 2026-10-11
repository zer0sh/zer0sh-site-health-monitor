<?php
declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Tests\Checks;

use Zer0sh\SiteHealthMonitor\Checks\AbstractCheck;
use Zer0sh\SiteHealthMonitor\Result;

final class DuplicateIdCheck extends AbstractCheck {

	public function get_id(): string {
		return 'test/duplicate';
	}

	public function get_title(): string {
		return 'Check con id duplicado';
	}

	public function run(): Result {
		return $this->result( Result::SEVERITY_OK, null );
	}
}
<?php
/**
 * Check de XML-RPC.
 *
 * @package Zer0shSiteHealthMonitor
 */

declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Checks;

use Zer0sh\SiteHealthMonitor\Result;
use Zer0sh\SiteHealthMonitor\ScoreDiscounts;

/**
 * Comprueba si XML-RPC está habilitado o expuesto, ya que se usa en
 * ataques de fuerza bruta contra el login.
 */
final class XmlRpcCheck extends AbstractCheck {

	/**
	 * Fuente que indica si XML-RPC está habilitado.
	 *
	 * @var callable
	 */
	private $enabled_source;

	/**
	 * Constructor con fuente inyectable para tests.
	 *
	 * @param callable|null $enabled_source Devuelve bool: XML-RPC habilitado.
	 */
	public function __construct( ?callable $enabled_source = null ) {
		$this->enabled_source = $enabled_source ?? static function (): bool {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core filter; its name must remain unchanged.
			return (bool) \apply_filters( 'xmlrpc_enabled', true );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_id(): string {
		return 'security/xmlrpc';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_title(): string {
		return __( 'XML-RPC', 'zer0sh-site-health-monitor' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function run(): Result {
		$enabled = (bool) call_user_func( $this->enabled_source );

		if ( ! $enabled ) {
			return $this->result( Result::SEVERITY_OK, $enabled );
		}

		return $this->result(
			Result::SEVERITY_WARNING,
			$enabled,
			__( 'XML-RPC is enabled and used in brute-force attacks. Disable it by returning false from the xmlrpc_enabled filter or blocking xmlrpc.php.', 'zer0sh-site-health-monitor' ),
			ScoreDiscounts::MINOR
		);
	}
}

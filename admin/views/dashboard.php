<?php
/**
 * Vista principal del panel de zer0sh Site Health Monitor.
 *
 * @package Zer0shSiteHealthMonitor
 *
 * @var array $data Datos preparados por DashboardData::build().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zer0sh_shm_has    = $data['has_result'];
$zer0sh_shm_state  = $zer0sh_shm_has ? $data['score_state'] : 'unknown';
$zer0sh_shm_gauge  = null !== $data['score_total'] ? (int) round( $data['score_total'] * 1.8 ) : 0;
$zer0sh_shm_format = $data['settings']->is_time_24h() ? 'H:i' : (string) get_option( 'time_format' );
$zer0sh_shm_values = array_merge(
	$data['site'],
	array(
		'vuln'    => $data['vulnerabilities_count'],
		'updates' => $data['pending_updates_count'],
	)
);
?>
<div class="wrap zer0sh-shm-wrap">
	<?php if ( 'ok' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'Scan completed successfully.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php elseif ( 'error' === $data['notice'] ) : ?>
		<div class="notice notice-error inline">
			<p><?php esc_html_e( 'The scan failed. Try again in a few seconds.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php elseif ( 'busy' === $data['notice'] ) : ?>
		<div class="notice notice-warning inline">
			<p><?php esc_html_e( 'A scan is already in progress.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php elseif ( 'ignored' === $data['notice'] ) : ?>
		<div class="notice notice-info is-dismissible inline">
			<p><?php esc_html_e( 'The finding has been moved to the ignored section.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php elseif ( 'restored' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'The finding has been restored and counts again in the diagnosis.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php elseif ( 'restored_all' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'All ignored findings have been restored.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php endif; ?>

	<header class="zer0sh-shm-header">
		<div class="zer0sh-shm-brand">
			<span class="zer0sh-shm-brand-mark" aria-hidden="true">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06" />
					<path d="M15 19l2 2l4 -4" />
				</svg>
			</span>
			<div class="zer0sh-shm-brand-text">
				<h1><?php esc_html_e( 'zer0sh Site Health Monitor', 'zer0sh-site-health-monitor' ); ?></h1>
				<span class="zer0sh-shm-brand-tag"><?php esc_html_e( 'Health, security and performance diagnosis', 'zer0sh-site-health-monitor' ); ?></span>
			</div>
		</div>

		<form class="zer0sh-shm-scan-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="zer0sh_shm_scan" />
			<?php wp_nonce_field( 'zer0sh_shm_scan' ); ?>
			<button type="submit" class="button button-primary zer0sh-shm-scan-button">
				<span><?php esc_html_e( 'Scan now', 'zer0sh-site-health-monitor' ); ?></span>
			</button>
		</form>
	</header>

	<?php if ( ! $zer0sh_shm_has ) : ?>
		<div class="notice notice-info inline">
			<p><?php esc_html_e( 'No scans yet. Click "Scan now" to generate the first diagnosis.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="zer0sh-shm-grid">
		<section class="zer0sh-shm-grid-col zer0sh-shm-col-main" aria-label="<?php esc_attr_e( 'Site summary', 'zer0sh-site-health-monitor' ); ?>">
			<div class="zer0sh-shm-card">
				<h2 class="zer0sh-shm-card-title"><?php esc_html_e( 'Health Score', 'zer0sh-site-health-monitor' ); ?></h2>
				<div class="zer0sh-shm-gauge zer0sh-shm-gauge-<?php echo esc_attr( $zer0sh_shm_state ); ?>" style="<?php echo esc_attr( '--p:' . $zer0sh_shm_gauge . 'deg' ); ?>">
					<div class="zer0sh-shm-gauge-fill"></div>
					<div class="zer0sh-shm-gauge-value">
						<?php
						if ( $zer0sh_shm_has ) {
							echo esc_html( (string) $data['score_total'] );
						} else {
							echo '&mdash;';
						}
						?>
					</div>
				</div>
				<div class="zer0sh-shm-card-meta">
					<?php echo esc_html( $zer0sh_shm_has ? $data['score_label'] : __( 'No scan', 'zer0sh-site-health-monitor' ) ); ?>
				</div>
			</div>

			<div class="zer0sh-shm-card">
				<h2 class="zer0sh-shm-card-title"><?php esc_html_e( 'Last scan', 'zer0sh-site-health-monitor' ); ?></h2>
				<div class="zer0sh-shm-card-value">
					<?php
					if ( $zer0sh_shm_has ) {
						echo esc_html(
							date_i18n(
								get_option( 'date_format' ) . ' ' . $zer0sh_shm_format,
								$data['completed_at']
							)
						);
					} else {
						echo '&mdash;';
					}
					?>
				</div>
				<div class="zer0sh-shm-card-meta">
					<?php echo esc_html( $zer0sh_shm_has ? $data['origin_label'] : __( 'No scans', 'zer0sh-site-health-monitor' ) ); ?>
				</div>
			</div>
		</section>

		<section class="zer0sh-shm-grid-col zer0sh-shm-col-site" aria-label="<?php esc_attr_e( 'Technical details', 'zer0sh-site-health-monitor' ); ?>">
			<h2 class="zer0sh-shm-grid-heading"><?php esc_html_e( 'Technical details', 'zer0sh-site-health-monitor' ); ?></h2>

			<div class="zer0sh-shm-card zer0sh-shm-tech">
				<span class="zer0sh-shm-tech-icon zer0sh-shm-tech-icon-globe" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
						<path d="M3.6 9h16.8" />
						<path d="M3.6 15h16.8" />
						<path d="M11.5 3a17 17 0 0 0 0 18" />
						<path d="M12.5 3a17 17 0 0 1 0 18" />
					</svg>
				</span>
				<div class="zer0sh-shm-tech-text">
					<div class="zer0sh-shm-tech-label"><?php esc_html_e( 'Site URL', 'zer0sh-site-health-monitor' ); ?></div>
					<div class="zer0sh-shm-tech-value zer0sh-shm-site-url">
						<a href="<?php echo esc_url( $zer0sh_shm_values['url'] ); ?>" target="_blank" rel="noreferrer noopener">
							<?php echo esc_html( $zer0sh_shm_values['url'] ); ?>
						</a>
					</div>
				</div>
			</div>

			<div class="zer0sh-shm-tech-pair">
				<div class="zer0sh-shm-card zer0sh-shm-tech">
					<span class="zer0sh-shm-tech-icon zer0sh-shm-tech-icon-wp" aria-hidden="true">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M9.5 9h3" />
							<path d="M4 9h2.5" />
							<path d="M11 9l3 11l4 -9" />
							<path d="M5.5 9l3.5 11l3 -7" />
							<path d="M18 11c.177 -.528 1 -1.364 1 -2.5c0 -1.78 -.776 -2.5 -1.875 -2.5c-.898 0 -1.125 .812 -1.125 1.429c0 1.83 2 2.058 2 3.571" />
							<path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
						</svg>
					</span>
					<div class="zer0sh-shm-tech-text">
						<div class="zer0sh-shm-tech-label"><?php esc_html_e( 'WordPress', 'zer0sh-site-health-monitor' ); ?></div>
						<div class="zer0sh-shm-tech-value"><?php echo esc_html( $zer0sh_shm_values['core'] ); ?></div>
					</div>
				</div>

				<div class="zer0sh-shm-card zer0sh-shm-tech">
					<span class="zer0sh-shm-tech-icon zer0sh-shm-tech-icon-php" aria-hidden="true">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M2 12a10 9 0 1 0 20 0a10 9 0 1 0 -20 0" />
							<path d="M5.5 15l.395 -1.974l.605 -3.026h1.32a1 1 0 0 1 .986 1.164l-.167 1a1 1 0 0 1 -.986 .836h-1.653" />
							<path d="M15.5 15l.395 -1.974l.605 -3.026h1.32a1 1 0 0 1 .986 1.164l-.167 1a1 1 0 0 1 -.986 .836h-1.653" />
							<path d="M12 7.5l-1 5.5" />
							<path d="M11.6 10h2.4l-.5 3" />
						</svg>
					</span>
					<div class="zer0sh-shm-tech-text">
						<div class="zer0sh-shm-tech-label"><?php esc_html_e( 'PHP', 'zer0sh-site-health-monitor' ); ?></div>
						<div class="zer0sh-shm-tech-value"><?php echo esc_html( $zer0sh_shm_values['php'] ); ?></div>
					</div>
				</div>
			</div>

			<div class="zer0sh-shm-card zer0sh-shm-tech">
				<span class="zer0sh-shm-tech-icon zer0sh-shm-tech-icon-server" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 7a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-2" />
						<path d="M3 15a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -2" />
						<path d="M7 8l0 .01" />
						<path d="M7 16l0 .01" />
					</svg>
				</span>
				<div class="zer0sh-shm-tech-text">
					<div class="zer0sh-shm-tech-label"><?php esc_html_e( 'Server', 'zer0sh-site-health-monitor' ); ?></div>
					<div class="zer0sh-shm-tech-value"><?php echo esc_html( '' !== $zer0sh_shm_values['server'] ? $zer0sh_shm_values['server'] : __( 'Not detectable', 'zer0sh-site-health-monitor' ) ); ?></div>
				</div>
			</div>
		</section>

		<section class="zer0sh-shm-grid-col zer0sh-shm-col-key" aria-label="<?php esc_attr_e( 'Key findings', 'zer0sh-site-health-monitor' ); ?>">
			<h2 class="zer0sh-shm-grid-heading"><?php esc_html_e( 'Key findings', 'zer0sh-site-health-monitor' ); ?></h2>

			<div class="zer0sh-shm-card zer0sh-shm-stat">
				<div class="zer0sh-shm-stat-value zer0sh-shm-stat-value-<?php echo esc_attr( $zer0sh_shm_values['vuln'] > 0 ? 'bad' : 'good' ); ?>">
					<?php echo esc_html( (string) $zer0sh_shm_values['vuln'] ); ?>
				</div>
				<div class="zer0sh-shm-stat-label"><?php esc_html_e( 'Active vulnerabilities', 'zer0sh-site-health-monitor' ); ?></div>
				<span class="zer0sh-shm-badge zer0sh-shm-badge-<?php echo esc_attr( $zer0sh_shm_values['vuln'] > 0 ? 'bad' : 'good' ); ?>">
					<?php echo esc_html( $zer0sh_shm_values['vuln'] > 0 ? __( 'At risk', 'zer0sh-site-health-monitor' ) : __( 'Safe', 'zer0sh-site-health-monitor' ) ); ?>
				</span>
			</div>

			<div class="zer0sh-shm-card zer0sh-shm-stat">
				<div class="zer0sh-shm-stat-value zer0sh-shm-stat-value-<?php echo esc_attr( $zer0sh_shm_values['updates'] > 0 ? 'bad' : 'good' ); ?>">
					<?php echo esc_html( (string) $zer0sh_shm_values['updates'] ); ?>
				</div>
				<div class="zer0sh-shm-stat-label"><?php esc_html_e( 'Pending updates', 'zer0sh-site-health-monitor' ); ?></div>
				<span class="zer0sh-shm-badge zer0sh-shm-badge-<?php echo esc_attr( $zer0sh_shm_values['updates'] > 0 ? 'warn' : 'good' ); ?>">
					<?php echo esc_html( $zer0sh_shm_values['updates'] > 0 ? __( 'Pending', 'zer0sh-site-health-monitor' ) : __( 'Up to date', 'zer0sh-site-health-monitor' ) ); ?>
				</span>
			</div>
		</section>
	</div>

	<?php if ( $zer0sh_shm_has && array() !== $data['categories'] ) : ?>
		<?php foreach ( $data['categories'] as $zer0sh_shm_group ) : ?>
			<section class="zer0sh-shm-section">
				<h2 class="zer0sh-shm-section-title"><?php echo esc_html( $zer0sh_shm_group['label'] ); ?></h2>
				<?php
				$zer0sh_shm_findings = $zer0sh_shm_group['findings'];
				$zer0sh_shm_restore  = false;
				require __DIR__ . '/partials/findings-table.php';
				?>
			</section>
		<?php endforeach; ?>
	<?php elseif ( $zer0sh_shm_has ) : ?>
		<div class="notice notice-success inline">
			<p><?php esc_html_e( 'No relevant findings detected.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $zer0sh_shm_has && $data['ignored_count'] > 0 ) : ?>
		<div class="zer0sh-shm-section">
			<details class="zer0sh-shm-ignored">
				<summary>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of ignored findings. */
							__( 'Ignored (%d)', 'zer0sh-site-health-monitor' ),
							$data['ignored_count']
						)
					);
					?>
					<span class="zer0sh-shm-restore-all">
						<a href="<?php echo esc_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::restore_all_url() ); ?>">
							<?php esc_html_e( 'Restore all', 'zer0sh-site-health-monitor' ); ?>
						</a>
					</span>
				</summary>
				<?php
				$zer0sh_shm_findings = $data['ignored'];
				$zer0sh_shm_restore  = true;
				require __DIR__ . '/partials/findings-table.php';
				?>
			</details>
		</div>
	<?php endif; ?>
</div>

<?php
/**
 * Tabla de hallazgos del panel de zer0sh Site Health Monitor.
 *
 * Vista parcial reutilizada por el dashboard para cada categoría y para
 * los hallazgos ignorados; todas las tablas se renderizan igual.
 *
 * @package Zer0shSiteHealthMonitor
 *
 * @var array $zer0sh_shm_findings Filas con el formato de DashboardData::finding().
 * @var bool  $zer0sh_shm_restore   true si las filas ya están ignoradas (acción "Restaurar").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zer0sh_shm_restore = isset( $zer0sh_shm_restore ) ? (bool) $zer0sh_shm_restore : false;
?>
<table class="zer0sh-shm-table">
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Finding', 'zer0sh-site-health-monitor' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Description', 'zer0sh-site-health-monitor' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Severity', 'zer0sh-site-health-monitor' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Action', 'zer0sh-site-health-monitor' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $zer0sh_shm_findings as $zer0sh_shm_finding ) : ?>
			<?php
			$zer0sh_shm_points_tip = $zer0sh_shm_finding['points'] > 0
				? sprintf(
					/* translators: %d: points deducted from the Health Score. */
					__( '-%d points', 'zer0sh-site-health-monitor' ),
					$zer0sh_shm_finding['points']
				)
				: '';
			$zer0sh_shm_is_group = isset( $zer0sh_shm_finding['vulnerabilities'] ) && is_array( $zer0sh_shm_finding['vulnerabilities'] );
			?>
			<tr>
				<td class="zer0sh-shm-finding-cell">
					<span class="zer0sh-shm-finding-title"><?php echo esc_html( $zer0sh_shm_finding['title'] ); ?></span>
					<span class="zer0sh-shm-info-icon" title="<?php echo esc_attr( $zer0sh_shm_finding['description'] ); ?>" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
							<path d="M12 9h.01" />
							<path d="M11 12h1v4h1" />
						</svg>
					</span>
					<br />
					<span class="zer0sh-shm-finding-id"><?php echo esc_html( $zer0sh_shm_finding['id'] ); ?></span>
					<?php if ( '' !== $zer0sh_shm_finding['value'] ) : ?>
						<br />
						<span class="zer0sh-shm-finding-value"><?php echo esc_html( $zer0sh_shm_finding['value'] ); ?></span>
					<?php endif; ?>
				</td>
				<td><?php echo esc_html( $zer0sh_shm_finding['recommendation'] ); ?></td>
				<td>
					<span class="zer0sh-shm-severity zer0sh-shm-severity-<?php echo esc_attr( $zer0sh_shm_finding['severity'] ); ?>"
						<?php if ( '' !== $zer0sh_shm_points_tip ) : ?>
							title="<?php echo esc_attr( $zer0sh_shm_points_tip ); ?>"
						<?php endif; ?>
					><?php echo esc_html( $zer0sh_shm_finding['severity_label'] ); ?></span>
				</td>
				<td class="zer0sh-shm-action-col">
					<?php if ( $zer0sh_shm_restore ) : ?>
						<a class="zer0sh-shm-ignore-link" href="<?php echo esc_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::toggle_ignored_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::UNIGNORE_ACTION, $zer0sh_shm_finding['id'] ) ); ?>">
							<?php esc_html_e( 'Restore', 'zer0sh-site-health-monitor' ); ?>
						</a>
					<?php else : ?>
						<?php if ( '' !== $zer0sh_shm_finding['link'] ) : ?>
							<a class="zer0sh-shm-action-link" href="<?php echo esc_url( $zer0sh_shm_finding['link'] ); ?>" target="_blank" rel="noreferrer noopener">
								<?php esc_html_e( 'View source', 'zer0sh-site-health-monitor' ); ?>
							</a>
						<?php elseif ( null !== $zer0sh_shm_finding['screen'] ) : ?>
							<a class="zer0sh-shm-action-link" href="<?php echo esc_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::screen_url( $zer0sh_shm_finding['screen'] ) ); ?>">
								<?php echo esc_html( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::screen_label( $zer0sh_shm_finding['screen'] ) ); ?>
							</a>
						<?php elseif ( ! $zer0sh_shm_finding['is_ok'] ) : ?>
							<span class="zer0sh-shm-action-disabled"><?php esc_html_e( 'No action available', 'zer0sh-site-health-monitor' ); ?></span>
						<?php endif; ?>
						<?php if ( ! $zer0sh_shm_finding['is_ok'] && ! $zer0sh_shm_is_group ) : ?>
							<br />
							<a class="zer0sh-shm-ignore-link" href="<?php echo esc_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::toggle_ignored_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::IGNORE_ACTION, $zer0sh_shm_finding['id'] ) ); ?>">
								<?php esc_html_e( 'Ignore', 'zer0sh-site-health-monitor' ); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if ( $zer0sh_shm_is_group ) : ?>
				<tr class="zer0sh-shm-sub-row">
					<td colspan="4">
						<details class="zer0sh-shm-sub-list">
							<summary><?php esc_html_e( 'Click for details', 'zer0sh-site-health-monitor' ); ?></summary>
							<ul class="zer0sh-shm-sub-list-items">
								<?php foreach ( $zer0sh_shm_finding['vulnerabilities'] as $zer0sh_shm_child ) : ?>
									<?php
									$zer0sh_shm_child_points_tip = $zer0sh_shm_child['points'] > 0
										? sprintf(
											/* translators: %d: points deducted from the Health Score. */
											__( '-%d points', 'zer0sh-site-health-monitor' ),
											$zer0sh_shm_child['points']
										)
										: '';
									?>
									<li>
										<span class="zer0sh-shm-sub-body">
											<span class="zer0sh-shm-sub-severity zer0sh-shm-severity zer0sh-shm-severity-<?php echo esc_attr( $zer0sh_shm_child['severity'] ); ?>"
												<?php if ( '' !== $zer0sh_shm_child_points_tip ) : ?>
													title="<?php echo esc_attr( $zer0sh_shm_child_points_tip ); ?>"
												<?php endif; ?>
											><?php echo esc_html( $zer0sh_shm_child['severity_label'] ); ?></span>
											<span class="zer0sh-shm-sub-title"><?php echo esc_html( $zer0sh_shm_child['title'] ); ?></span>
										<span class="zer0sh-shm-info-icon" title="<?php echo esc_attr( $zer0sh_shm_child['description'] ); ?>" aria-hidden="true">
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
												<path d="M12 9h.01" />
												<path d="M11 12h1v4h1" />
											</svg>
										</span>
											<br />
											<span class="zer0sh-shm-sub-id"><?php echo esc_html( $zer0sh_shm_child['id'] ); ?></span>
											<span class="zer0sh-shm-sub-meta"><?php echo esc_html( $zer0sh_shm_child['recommendation'] ); ?></span>
										</span>
										<span class="zer0sh-shm-sub-actions">
											<?php if ( '' !== $zer0sh_shm_child['link'] ) : ?>
												<a class="zer0sh-shm-action-link" href="<?php echo esc_url( $zer0sh_shm_child['link'] ); ?>" target="_blank" rel="noreferrer noopener">
													<?php esc_html_e( 'View source', 'zer0sh-site-health-monitor' ); ?>
												</a>
											<?php endif; ?>
											<?php if ( $zer0sh_shm_restore ) : ?>
												<a class="zer0sh-shm-ignore-link" href="<?php echo esc_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::toggle_ignored_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::UNIGNORE_ACTION, $zer0sh_shm_child['id'] ) ); ?>">
													<?php esc_html_e( 'Restore', 'zer0sh-site-health-monitor' ); ?>
												</a>
											<?php elseif ( ! $zer0sh_shm_child['is_ok'] ) : ?>
												<a class="zer0sh-shm-ignore-link" href="<?php echo esc_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::toggle_ignored_url( \Zer0sh\SiteHealthMonitor\Admin\AdminPage::IGNORE_ACTION, $zer0sh_shm_child['id'] ) ); ?>">
													<?php esc_html_e( 'Ignore', 'zer0sh-site-health-monitor' ); ?>
												</a>
											<?php endif; ?>
										</span>
									</li>
								<?php endforeach; ?>
							</ul>
						</details>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>

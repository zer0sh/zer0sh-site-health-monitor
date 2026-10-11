<?php
/**
 * Vista de ajustes de zer0sh Site Health Monitor.
 *
 * @package Zer0shSiteHealthMonitor
 *
 * @var array $data Datos preparados por AdminPage::render_settings().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zer0sh_shm_settings = $data['settings'];
?>
<div class="wrap zer0sh-shm-wrap">
	<h1><?php esc_html_e( 'zer0sh Site Health Monitor Settings', 'zer0sh-site-health-monitor' ); ?></h1>

	<?php if ( 'updated' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'Settings saved successfully.', 'zer0sh-site-health-monitor' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="zer0sh-shm-frequency"><?php esc_html_e( 'Scan frequency', 'zer0sh-site-health-monitor' ); ?></label>
				</th>
				<td>
					<select name="zer0sh_shm[frequency]" id="zer0sh-shm-frequency">
						<?php
						$zer0sh_shm_frequencies = array(
							'disabled' => __( 'Disabled', 'zer0sh-site-health-monitor' ),
							'daily'    => __( 'Daily', 'zer0sh-site-health-monitor' ),
							'weekly'   => __( 'Weekly', 'zer0sh-site-health-monitor' ),
							'monthly'  => __( 'Monthly', 'zer0sh-site-health-monitor' ),
						);

						foreach ( $zer0sh_shm_frequencies as $zer0sh_shm_value => $zer0sh_shm_label ) :
							?>
							<option value="<?php echo esc_attr( $zer0sh_shm_value ); ?>" <?php selected( $zer0sh_shm_settings->get_frequency(), $zer0sh_shm_value ); ?>>
								<?php echo esc_html( $zer0sh_shm_label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php esc_html_e( 'How often the site status is checked automatically.', 'zer0sh-site-health-monitor' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="zer0sh-shm-mail-enabled"><?php esc_html_e( 'Email reports', 'zer0sh-site-health-monitor' ); ?></label>
				</th>
				<td>
					<label for="zer0sh-shm-mail-enabled">
						<input
							type="checkbox"
							name="zer0sh_shm[mail_enabled]"
							id="zer0sh-shm-mail-enabled"
							value="1"
							<?php checked( $zer0sh_shm_settings->is_mail_enabled(), true ); ?>
						/>
						<?php esc_html_e( 'Send results by email', 'zer0sh-site-health-monitor' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'After each manual scan and when the diagnosis changes in scheduled ones.', 'zer0sh-site-health-monitor' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="zer0sh-shm-recipient"><?php esc_html_e( 'Recipient', 'zer0sh-site-health-monitor' ); ?></label>
				</th>
				<td>
					<input
						type="email"
						name="zer0sh_shm[recipient]"
						id="zer0sh-shm-recipient"
						class="regular-text"
						value="<?php echo esc_attr( $zer0sh_shm_settings->get_recipient() ); ?>"
						placeholder="<?php echo esc_attr( (string) get_option( 'admin_email', '' ) ); ?>"
					/>
					<p class="description">
						<?php
						printf(
							/* translators: %s: default administrator email. */
							esc_html__( 'Leave empty to use the administrator email («%s»).', 'zer0sh-site-health-monitor' ),
							esc_html( (string) get_option( 'admin_email', '' ) )
						);
						?>
					</p>
				</td>
			</tr>
		<tr>
				<th scope="row">
					<label for="zer0sh-shm-time-24h"><?php esc_html_e( 'Time format', 'zer0sh-site-health-monitor' ); ?></label>
				</th>
				<td>
					<label for="zer0sh-shm-time-24h">
						<input
							type="checkbox"
							name="zer0sh_shm[time_24h]"
							id="zer0sh-shm-time-24h"
							value="1"
							<?php checked( $zer0sh_shm_settings->is_time_24h(), true ); ?>
						/>
						<?php esc_html_e( 'Use 24-hour format', 'zer0sh-site-health-monitor' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Shows scan and scheduling times in 24-hour format.', 'zer0sh-site-health-monitor' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<?php wp_nonce_field( 'zer0sh_shm_settings' ); ?>
		<input type="hidden" name="action" value="zer0sh_shm_settings" />
		<?php submit_button( __( 'Save settings', 'zer0sh-site-health-monitor' ), 'primary', 'submit', false ); ?>
	</form>

	<div class="notice notice-info inline zer0sh-shm-feature-request">
		<p><strong><?php esc_html_e( 'Have a feature request?', 'zer0sh-site-health-monitor' ); ?></strong></p>
		<p>
			<?php esc_html_e( 'Create an issue in the repository or send me an email at', 'zer0sh-site-health-monitor' ); ?>
			<a href="<?php echo esc_url( 'https://github.com/zer0-sh/zer0sh-site-health-monitor/issues/new' ); ?>"><?php esc_html_e( 'Create an issue', 'zer0sh-site-health-monitor' ); ?></a>
			<?php esc_html_e( 'or', 'zer0sh-site-health-monitor' ); ?>
			<a href="<?php echo esc_url( 'mailto:zer0sh@protonmail.ch?subject=zer0sh%20Site%20Health%20Monitor%20feature%20request' ); ?>">zer0sh@protonmail.ch</a>
			<?php esc_html_e( 'with your request. I will work on it as soon as I have some time.', 'zer0sh-site-health-monitor' ); ?>
		</p>
	</div>

	<?php $zer0sh_shm_time_format = $zer0sh_shm_settings->is_time_24h() ? 'H:i' : (string) get_option( 'time_format' ); ?>

	<p>
		<strong>
			<?php
			printf(
				/* translators: 1: configured frequency, 2: date of the next run. */
				esc_html__( 'Scheduled scan: %1$s. Next run: %2$s.', 'zer0sh-site-health-monitor' ),
				esc_html( $zer0sh_shm_frequencies[ $zer0sh_shm_settings->get_frequency() ] ),
				null !== $data['next_run']
					? esc_html(
						date_i18n(
							get_option( 'date_format' ) . ' ' . $zer0sh_shm_time_format,
							$data['next_run']
						)
					)
					: esc_html__( 'not scheduled', 'zer0sh-site-health-monitor' )
			);
			?>
		</strong>
	</p>
</div>

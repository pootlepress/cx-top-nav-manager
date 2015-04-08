<?php

/**
 * Admin Menu Class
 *
 * @package Update API Manager/Admin
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @since 1.3
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PootlePress_Top_Nav_License_MENU {

	// Load admin menu
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'load_settings' ) );
	}

	// Add option page menu
	public function add_menu() {

		$page = add_options_page( __( PPTN_License()->ame_settings_menu_title, PPTN_License()->text_domain ), __( PPTN_License()->ame_settings_menu_title, PPTN_License()->text_domain ),
						'manage_options', PPTN_License()->ame_activation_tab_key, array( $this, 'config_page')
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'css_scripts' ) );
	}

	// Draw option page
	public function config_page() {

		$settings_tabs = array( PPTN_License()->ame_activation_tab_key => __( PPTN_License()->ame_menu_tab_activation_title, PPTN_License()->text_domain ), PPTN_License()->ame_deactivation_tab_key => __( PPTN_License()->ame_menu_tab_deactivation_title, PPTN_License()->text_domain ) );
		$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : PPTN_License()->ame_activation_tab_key;
		$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : PPTN_License()->ame_activation_tab_key;
		?>
		<div class='wrap'>
			<?php screen_icon(); ?>
			<h2><?php _e( PPTN_License()->ame_settings_title, PPTN_License()->text_domain ); ?></h2>

			<h2 class="nav-tab-wrapper">
			<?php
				foreach ( $settings_tabs as $tab_page => $tab_name ) {
					$active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
					echo '<a class="nav-tab ' . $active_tab . '" href="?page=' . PPTN_License()->ame_activation_tab_key . '&tab=' . $tab_page . '">' . $tab_name . '</a>';
				}
			?>
			</h2>
				<form action='options.php' method='post'>
					<div class="main">
				<?php
					if( $tab == PPTN_License()->ame_activation_tab_key ) {
							settings_fields( PPTN_License()->ame_data_key );
							do_settings_sections( PPTN_License()->ame_activation_tab_key );
							submit_button( __( 'Save Changes', PPTN_License()->text_domain ) );
					} else {
							settings_fields( PPTN_License()->ame_deactivate_checkbox );
							do_settings_sections( PPTN_License()->ame_deactivation_tab_key );
							submit_button( __( 'Save Changes', PPTN_License()->text_domain ) );
					}
				?>
					</div>
				</form>
			</div>
			<?php
	}

	// Register settings
	public function load_settings() {

		register_setting( PPTN_License()->ame_data_key, PPTN_License()->ame_data_key, array( $this, 'validate_options' ) );

		// API Key
		add_settings_section( PPTN_License()->ame_api_key, __( 'License Activation', PPTN_License()->text_domain ), array( $this, 'wc_am_api_key_text' ), PPTN_License()->ame_activation_tab_key );
		add_settings_field( 'status', __( 'License Key Status', PPTN_License()->text_domain ), array( $this, 'wc_am_api_key_status' ), PPTN_License()->ame_activation_tab_key, PPTN_License()->ame_api_key );
		add_settings_field( PPTN_License()->ame_api_key, __( 'License Key', PPTN_License()->text_domain ), array( $this, 'wc_am_api_key_field' ), PPTN_License()->ame_activation_tab_key, PPTN_License()->ame_api_key );
		add_settings_field( PPTN_License()->ame_activation_email, __( 'License email', PPTN_License()->text_domain ), array( $this, 'wc_am_api_email_field' ), PPTN_License()->ame_activation_tab_key, PPTN_License()->ame_api_key );

		// Activation settings
		register_setting( PPTN_License()->ame_deactivate_checkbox, PPTN_License()->ame_deactivate_checkbox, array( $this, 'wc_am_license_key_deactivation' ) );
		add_settings_section( 'deactivate_button', __( 'License Deactivation', PPTN_License()->text_domain ), array( $this, 'wc_am_deactivate_text' ), PPTN_License()->ame_deactivation_tab_key );
		add_settings_field( 'deactivate_button', __( 'Deactivate License Key', PPTN_License()->text_domain ), array( $this, 'wc_am_deactivate_textarea' ), PPTN_License()->ame_deactivation_tab_key, 'deactivate_button' );

	}

	// Provides text for api key section
	public function wc_am_api_key_text() {
		//
	}

	// Returns the API License Key status from the WooCommerce API Manager on the server
	public function wc_am_api_key_status() {
		$license_status = $this->license_key_status();
		$license_status_check = ( ! empty( $license_status['status_check'] ) && $license_status['status_check'] == 'active' ) ? 'Activated' : 'Deactivated';
		if ( ! empty( $license_status_check ) ) {
			echo $license_status_check;
		}
	}

	// Returns API License text field
	public function wc_am_api_key_field() {

		echo "<input id='api_key' name='" . PPTN_License()->ame_data_key . "[" . PPTN_License()->ame_api_key ."]' size='25' type='text' value='" . PPTN_License()->ame_options[PPTN_License()->ame_api_key] . "' />";
		if ( PPTN_License()->ame_options[PPTN_License()->ame_api_key] ) {
			echo "<span class='icon-pos'><img src='" . PPTN_License()->plugin_url() . "api/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . PPTN_License()->plugin_url() . "api/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Returns API License email text field
	public function wc_am_api_email_field() {

		echo "<input id='activation_email' name='" . PPTN_License()->ame_data_key . "[" . PPTN_License()->ame_activation_email ."]' size='25' type='text' value='" . PPTN_License()->ame_options[PPTN_License()->ame_activation_email] . "' />";
		if ( PPTN_License()->ame_options[PPTN_License()->ame_activation_email] ) {
			echo "<span class='icon-pos'><img src='" . PPTN_License()->plugin_url() . "api/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . PPTN_License()->plugin_url() . "api/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_options( $input ) {

		// Load existing options, validate, and update with changes from input before returning
		$options = PPTN_License()->ame_options;

		$options[PPTN_License()->ame_api_key] = trim( $input[PPTN_License()->ame_api_key] );
		$options[PPTN_License()->ame_activation_email] = trim( $input[PPTN_License()->ame_activation_email] );

		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input[PPTN_License()->ame_activation_email] );
		$api_key = trim( $input[PPTN_License()->ame_api_key] );

		$activation_status = get_option( PPTN_License()->ame_activated_key );
		$checkbox_status = get_option( PPTN_License()->ame_deactivate_checkbox );

		$current_api_key = PPTN_License()->ame_options[PPTN_License()->ame_api_key];

		// Should match the settings_fields() value
		if ( $_REQUEST['option_page'] != PPTN_License()->ame_deactivate_checkbox ) {

			if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {

				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key )
					$this->replace_license_key( $current_api_key );

				$args = array(
					'email' => $api_email,
					'licence_key' => $api_key,
					);

				$activate_results = json_decode( PPTN_License()->key()->activate( $args ), true );

				if ( $activate_results['activated'] === true ) {
					add_settings_error( 'activate_text', 'activate_msg', __( 'Plugin activated. ', PPTN_License()->text_domain ) . "{$activate_results['message']}.", 'updated' );
					update_option( PPTN_License()->ame_activated_key, 'Activated' );
					update_option( PPTN_License()->ame_deactivate_checkbox, 'off' );
				}

				if ( $activate_results == false ) {
					add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Try again later.', PPTN_License()->text_domain ), 'error' );
					$options[PPTN_License()->ame_api_key] = '';
					$options[PPTN_License()->ame_activation_email] = '';
					update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
				}

				if ( isset( $activate_results['code'] ) ) {

					switch ( $activate_results['code'] ) {
						case '100':
							add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_activation_email] = '';
							$options[PPTN_License()->ame_api_key] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
						case '101':
							add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_api_key] = '';
							$options[PPTN_License()->ame_activation_email] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
						case '102':
							add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_api_key] = '';
							$options[PPTN_License()->ame_activation_email] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
						case '103':
								add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options[PPTN_License()->ame_api_key] = '';
								$options[PPTN_License()->ame_activation_email] = '';
								update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
						case '104':
								add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options[PPTN_License()->ame_api_key] = '';
								$options[PPTN_License()->ame_activation_email] = '';
								update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
						case '105':
								add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options[PPTN_License()->ame_api_key] = '';
								$options[PPTN_License()->ame_activation_email] = '';
								update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
						case '106':
								add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options[PPTN_License()->ame_api_key] = '';
								$options[PPTN_License()->ame_activation_email] = '';
								update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
						break;
					}

				}

			} // End Plugin Activation

		}

		return $options;
	}

	// Returns the API License Key status from the WooCommerce API Manager on the server
	public function license_key_status() {
		$activation_status = get_option( PPTN_License()->ame_activated_key );

		$args = array(
			'email' => PPTN_License()->ame_options[PPTN_License()->ame_activation_email],
			'licence_key' => PPTN_License()->ame_options[PPTN_License()->ame_api_key],
			);

		return json_decode( PPTN_License()->key()->status( $args ), true );
	}

	// Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {

		$args = array(
			'email' => PPTN_License()->ame_options[PPTN_License()->ame_activation_email],
			'licence_key' => $current_api_key,
			);

		$reset = PPTN_License()->key()->deactivate( $args ); // reset license key activation

		if ( $reset == true )
			return true;

		return add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', PPTN_License()->text_domain ), 'updated' );
	}

	// Deactivates the license key to allow key to be used on another blog
	public function wc_am_license_key_deactivation( $input ) {

		$activation_status = get_option( PPTN_License()->ame_activated_key );

		$args = array(
			'email' => PPTN_License()->ame_options[PPTN_License()->ame_activation_email],
			'licence_key' => PPTN_License()->ame_options[PPTN_License()->ame_api_key],
			);

		// For testing activation status_extra data
		// $activate_results = json_decode( PPTN_License()->key()->status( $args ), true );
		// print_r($activate_results); exit;

		$options = ( $input == 'on' ? 'on' : 'off' );

		if ( $options == 'on' && $activation_status == 'Activated' && PPTN_License()->ame_options[PPTN_License()->ame_api_key] != '' && PPTN_License()->ame_options[PPTN_License()->ame_activation_email] != '' ) {

			// deactivates license key activation
			$activate_results = json_decode( PPTN_License()->key()->deactivate( $args ), true );

			// Used to display results for development
			//print_r($activate_results); exit();

			if ( $activate_results['deactivated'] === true ) {
				$update = array(
					PPTN_License()->ame_api_key => '',
					PPTN_License()->ame_activation_email => ''
					);

				$merge_options = array_merge( PPTN_License()->ame_options, $update );

				update_option( PPTN_License()->ame_data_key, $merge_options );

				update_option( PPTN_License()->ame_activated_key, 'Deactivated' );

				add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg', __( 'Plugin license deactivated. ', PPTN_License()->text_domain ) . "{$activate_results['activations_remaining']}.", 'updated' );

				return $options;
			}

			if ( isset( $activate_results['code'] ) ) {

				switch ( $activate_results['code'] ) {
					case '100':
						add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options[PPTN_License()->ame_activation_email] = '';
						$options[PPTN_License()->ame_api_key] = '';
						update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
					case '101':
						add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options[PPTN_License()->ame_api_key] = '';
						$options[PPTN_License()->ame_activation_email] = '';
						update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
					case '102':
						add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options[PPTN_License()->ame_api_key] = '';
						$options[PPTN_License()->ame_activation_email] = '';
						update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
					case '103':
							add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_api_key] = '';
							$options[PPTN_License()->ame_activation_email] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
					case '104':
							add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_api_key] = '';
							$options[PPTN_License()->ame_activation_email] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
					case '105':
							add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_api_key] = '';
							$options[PPTN_License()->ame_activation_email] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
					case '106':
							add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[PPTN_License()->ame_api_key] = '';
							$options[PPTN_License()->ame_activation_email] = '';
							update_option( PPTN_License()->ame_options[PPTN_License()->ame_activated_key], 'Deactivated' );
					break;
				}

			}

		} else {

			return $options;
		}

	}

	public function wc_am_deactivate_text() {
	}

	public function wc_am_deactivate_textarea() {

		echo '<input type="checkbox" id="' . PPTN_License()->ame_deactivate_checkbox . '" name="' . PPTN_License()->ame_deactivate_checkbox . '" value="on"';
		echo checked( get_option( PPTN_License()->ame_deactivate_checkbox ), 'on' );
		echo '/>';
		?><span class="description"><?php _e( 'Deactivates an API License Key so it can be used on another blog.', PPTN_License()->text_domain ); ?></span>
		<?php
	}

	// Loads admin style sheets
	public function css_scripts() {

		wp_register_style( PPTN_License()->ame_data_key . '-css', PPTN_License()->plugin_url() . 'api/assets/css/admin-settings.css', array(), PPTN_License()->version, 'all');
		wp_enqueue_style( PPTN_License()->ame_data_key . '-css' );
	}

}

new PootlePress_Top_Nav_License_MENU();

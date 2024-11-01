<?php

/**
 * Plugin Name:  SubsBase Integration
 * Description:  Manage your integration with SubsBase's Subscription Management Platform.
 * Version:      1.3.3
 * Author:       SubsBase
 * Author URI:   https://subsbase.com/
 * Text Domain:  sbis
 * Domain Path:  /languages/
 * License: GPLv2 or later
 * License URI: https://opensource.org/licenses/GPL-2.0
 */

if (!defined('ABSPATH')) {
	exit; //prevent direct access to this file
}

define('SBIS_VERSION', '1.3.3');
define('SBIS_DIR', plugin_dir_path(__FILE__));
define('SBIS_URI', plugin_dir_url(__FILE__));

/**
 * Load plugin textdomain.
 */
add_action('init', 'sbis_load_textdomain');
function sbis_load_textdomain()
{
	load_plugin_textdomain('sbis', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_filter('plugin_row_meta', 'sbis_plugin_links', 10, 4);
function sbis_plugin_links($plugin_meta, $plugin_file, $plugin_data, $status)
{
	$sbse_plugin = plugin_basename(__FILE__);
	if (strpos($plugin_file, $sbse_plugin) !== false) {
		$new_links = array(
			'sbis_doc' => '<a href="https://docs.subsbase.com" target="_blank">' . __('View Documentation', 'sbis') . '</a>'
		);

		$plugin_meta = array_merge($plugin_meta, $new_links);
	}

	return $plugin_meta;
}

add_action('admin_menu', 'sbis_admin_menu_item');
function sbis_admin_menu_item()
{
	add_options_page(__('SubsBase Integration Settings', 'sbis'), __('SubsBase Integration Settings', 'sbis'), 'manage_options', 'sbis_embed_settings', 'sbis_admin_menu_page');
}

function sbis_admin_menu_page()
{
	echo '<div class="wrap">';
	include SBIS_DIR . '/settings-page.php';
	echo '</div>';
}

add_action('admin_enqueue_scripts', 'sbis_plugin_scripts');
function sbis_plugin_scripts()
{
	$screen = get_current_screen();
	if ($screen && $screen->id === 'settings_page_sbis_embed_settings') {
		wp_enqueue_style('sbis-styles', SBIS_URI . '/scripts/css/style.css', array('wp-color-picker'), SBIS_VERSION);
		wp_enqueue_script('sbis-repeater-script', SBIS_URI . '/scripts/js/libs.min.js', array('jquery'), SBIS_VERSION, true);
		wp_enqueue_script('sbis-main-script', SBIS_URI . '/scripts/js/main.js', array(
			'jquery',
			'jquery-ui-tabs',
			'wp-color-picker',
			'sbis-repeater-script'
		), SBIS_VERSION, true);
		wp_localize_script('sbis-main-script', 'sbis_obj', array(
			'btn_flash_delay_validation' => __('Button flash delay value must be greater than button show delay value.', 'sbis')
		));
	}
}

function sbis_sanitize_array($data)
{
	if (!is_array($data) || !count($data)) {
		return array();
	}

	foreach ($data as $key => $value) {
		if (is_array($value)) {
			$data[sanitize_text_field($key)] = sbis_sanitize_array($value);
		} else {
			$data[sanitize_text_field($key)] = sanitize_text_field($value);
		}
	}

	return $data;
}

add_action('admin_post_sbis_settings', 'sbis_process_settings');
function sbis_process_settings()
{
	//todo sanitize checkout and parameters

	if (!isset($_POST['_wpnonce']) || empty($_POST['_wpnonce'])) {
		//do not execute saving process
		wp_die(esc_html__('Bad security check result, please try again', 'sbis'));
	}

	if (!wp_verify_nonce($_POST['_wpnonce'])) {
		wp_die(esc_html__('Bad security check result, please try again', 'sbis'));
	}

	if (!current_user_can('manage_options')) {
		wp_die(esc_html__('You don\'t have sufficient permissions to do this action!', 'sbis'));
	}

	$args = array(
		'site_id'                 => isset($_POST['site_id']) ? sanitize_text_field($_POST['site_id']) : '',
		'checkout_version'        => 'v2',
		'color_scheme'            => isset($_POST['color_scheme']) ? sanitize_text_field($_POST['color_scheme']) : '',
		'config_botton_position'  => isset($_POST['config_botton_position']) ? sanitize_text_field($_POST['config_botton_position']) : '',
		'config_botton_alignment' => isset($_POST['config_botton_alignment']) ? sanitize_text_field($_POST['config_botton_alignment']) : '',
		'btn_show_delay'          => isset($_POST['btn_show_delay']) ? sanitize_text_field($_POST['btn_show_delay']) : '0',
		'btn_flash_delay'         => isset($_POST['btn_flash_delay']) ? sanitize_text_field($_POST['btn_flash_delay']) : '0',
		'button_text'             => isset($_POST['button_text']) ? sanitize_text_field($_POST['button_text']) : '',
		'background_color'        => isset($_POST['background_color']) ? sanitize_text_field($_POST['background_color']) : '',
		'text_color'              => isset($_POST['text_color']) ? sanitize_text_field($_POST['text_color']) : '',
		'config_shape'            => isset($_POST['config_shape']) ? sanitize_text_field($_POST['config_shape']) : '',
		'config_icon_code'        => isset($_POST['config_icon_code']) ? sanitize_textarea_field($_POST['config_icon_code']) : '',
		'js_callback'             => $_POST['js_callback'] ?? '',
		'attach_checkout'         => isset($_POST['attach_checkout']) ? 1 : 0,
		'attach_plan_picker'      => isset($_POST['attach_plan_picker']) ? 1 : 0,
		'up_right_text'           => isset($_POST['up_right_text']) ? 1 : 0,
		'attach_callback'         => isset($_POST['attach_callback']) ? 1 : 0,
		'billing_cycle_group'     => isset($_POST['billing_cycle_group']) ? 1 : 0,
		'hide_all_group'          => isset($_POST['hide_all_group']) ? 1 : 0,
		'expanded_plan_card'      => isset($_POST['expanded_plan_card']) ? 1 : 0,
		'plan_card_collapse'      => isset($_POST['plan_card_collapse']) ? 1 : 0,
		'plan_card_sort'      		=> isset($_POST['plan_card_sort']) ? 1 : 0,
		'plan_card_defaultSort'   => isset($_POST['plan_card_defaultSort']) ? sanitize_text_field($_POST['plan_card_defaultSort']) : 'price:ascending',
		'success_url'             => isset($_POST['success_url']) ? sanitize_text_field($_POST['success_url']) : '',
		'checkout'                => sbis_sanitize_array($_POST['checkout']) ?? array(),
		'parameters'              => sbis_sanitize_array($_POST['parameters']) ?? array(),
		'custom_fields'           => sbis_sanitize_array($_POST['info']) ?? array(),
		'mobile_config' 		      => isset($_POST['mobile_config']) ? sanitize_text_field($_POST['mobile_config']) : '',
		'mob_config_botton_position'  => isset($_POST['mob_config_botton_position']) ? sanitize_text_field($_POST['mob_config_botton_position']) : '',
		'mob_config_botton_alignment' => isset($_POST['mob_config_botton_alignment']) ? sanitize_text_field($_POST['mob_config_botton_alignment']) : '',
		'mob_btn_show_delay'          => isset($_POST['mob_btn_show_delay']) ? sanitize_text_field($_POST['mob_btn_show_delay']) : '0',
		'mob_btn_flash_delay'         => isset($_POST['mob_btn_flash_delay']) ? sanitize_text_field($_POST['mob_btn_flash_delay']) : '0',
		'mob_button_text'             => isset($_POST['mob_button_text']) ? sanitize_text_field($_POST['mob_button_text']) : '',
		'mob_background_color'        => isset($_POST['mob_background_color']) ? sanitize_text_field($_POST['mob_background_color']) : '',
		'mob_text_color'              => isset($_POST['mob_text_color']) ? sanitize_text_field($_POST['mob_text_color']) : '',
		'mob_config_shape'            => isset($_POST['mob_config_shape']) ? sanitize_text_field($_POST['mob_config_shape']) : '',
		'mob_config_icon_code'        => isset($_POST['mob_config_icon_code']) ? sanitize_textarea_field($_POST['mob_config_icon_code']) : '',
		'mob_up_right_text'           => isset($_POST['mob_up_right_text']) ? 1 : 0,
		'mob_billing_cycle_group'     => isset($_POST['mob_billing_cycle_group']) ? 1 : 0,
		'mob_hide_all_group'          => isset($_POST['mob_hide_all_group']) ? 1 : 0,
		'mob_expanded_plan_card'      => isset($_POST['mob_expanded_plan_card']) ? 1 : 0,
		'mob_plan_card_collapse'      => isset($_POST['mob_plan_card_collapse']) ? 1 : 0,
		'mob_plan_card_sort'      		=> isset($_POST['mob_plan_card_sort']) ? 1 : 0,
		'mob_plan_card_defaultSort'   => isset($_POST['mob_plan_card_defaultSort']) ? sanitize_text_field($_POST['mob_plan_card_defaultSort']) : 'price:ascending',
	);

	update_option('sbse_embed_values', $args);
	set_transient('sbis_result_' . get_current_user_id(), array(
		'result' => 'success',
		'value'  => __('Settings saved successfully', 'sbis')
	), 5);
	wp_safe_redirect(esc_url(admin_url('options-general.php?page=sbis_embed_settings')));
	exit;
}

add_action('wp_head', 'sbis_output_embed');

function sbis_output_embed()
{
	$config = get_option('sbse_embed_values');
	if (!$config || empty($config)) {
		return false;
	}
?>
	<script>
		(function(d, o, s, a, m) {
			a = d.createElement(o);
			m = d.getElementsByTagName(o)[0];
			a.async = 1;
			a.defer = 1;
			a.src = s;
			m.parentNode.insertBefore(a, m)
		})(document, "script", "https://embed.subsbase.com/sb.min.js");
		window.sb = window.sb || function() {
			(sb.s = sb.s || []).push(arguments)
		};
		sb("siteId", "<?php echo isset($config['site_id']) ? esc_js($config['site_id']) : ''; ?>");
		sb('checkoutVersion', '<?php echo esc_js($config['checkout_version']); ?>');		
		<?php if (isset($config['color_scheme']) && !empty($config['color_scheme'])) { ?>
			sb('theme', '<?php echo esc_js($config['color_scheme']); ?>');
		<?php } ?>
		<?php if (isset($config['attach_callback'], $config['js_callback']) && $config['attach_callback'] == 1 && !empty($config['js_callback'])) { ?>
			sb("callback", <?php echo $config['js_callback']; ?>);
		<?php } ?>
		<?php if (isset($config['attach_checkout']) && $config['attach_checkout'] == 1) {
			if (isset($config['checkout']) && is_array($config['checkout'])) {
				foreach ($config['checkout'] as $checkout) {
					if ((isset($checkout['plan_code']) && $checkout['plan_code'] != '') && (isset($checkout['selector']) && $checkout['selector'] != '')) { ?>
						sb("attachPlan", "<?php echo isset($checkout['plan_code']) ? esc_attr($checkout['plan_code']) : '' ?>", "<?php echo isset($checkout['selector']) ? esc_attr($checkout['selector']) : '' ?>", "<?php echo isset($checkout['selector_type']) ? esc_attr($checkout['selector_type']) : 'id' ?>", "<?php echo isset($checkout['event']) ? esc_attr($checkout['event']) : 'click' ?>");
					<?php }
			 	}
			} ?>
			<?php if (isset($config['parameters']) && is_array($config['parameters'])) {
				foreach ($config['parameters'] as $parameter) {
					if (isset($parameter['key']) && !empty($parameter['key'])) { ?>
						sb('queryParam', "infoField[<?php echo isset($parameter['key']) ? esc_attr($parameter['key']) : ''  ?>]", "<?php echo isset($parameter['value']) ? esc_attr($parameter['value']) : '' ?>");
					<?php }
					}
			} ?>
			<?php if (isset($config['custom_fields']) && is_array($config['custom_fields'])) {
				foreach ($config['custom_fields'] as $parameter) {
					if (isset($parameter['key']) && !empty($parameter['key'])) { ?>
						sb('queryParam', "customField[<?php echo isset($parameter['key']) ? esc_attr($parameter['key']) : ''  ?>]", "<?php echo isset($parameter['value']) ? esc_attr($parameter['value']) : '' ?>");
					<?php }
				}
			} ?>
			<?php if (isset($config['success_url'])) { ?>
				sb("queryParam", "redirects[success]", "<?php echo $config['success_url']; ?>");
			<?php } ?>
		<?php } ?>			
		<?php if (isset($config['attach_plan_picker']) && $config['attach_plan_picker'] == 1) { ?>
			sb("attachPlanPicker", {
				shape: '<?php echo isset($config['config_shape']) ? esc_js($config['config_shape']) : 'rectangle'; ?>',
				icon: '<?php echo isset($config['config_icon_code']) ? esc_js($config['config_icon_code']) : 'data:image/png;base64,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'; ?>',
				text: '<?php echo isset($config['button_text']) ? esc_js($config['button_text']) : __('Subscribe', 'sbis'); ?>',
				textColor: '<?php echo isset($config['text_color']) ? esc_js($config['text_color']) : '#f7b547'; ?>',
				backgroundColor: '<?php echo isset($config['background_color']) ? esc_js($config['background_color']) : '#20407d'; ?>',
				position: '<?php echo isset($config['config_botton_position']) ? esc_js($config['config_botton_position']) : 'right'; ?>',
				alignment: '<?php echo isset($config['config_botton_alignment']) ? esc_js($config['config_botton_alignment']) : 'center'; ?>',
				uprightText: <?php echo isset($config['up_right_text']) && $config['up_right_text'] != 1 ? 'false' : 'true'; ?>,
				showDelay: <?php echo isset($config['btn_show_delay']) && !empty($config['btn_show_delay']) ? esc_html($config['btn_show_delay']) : '0'; ?>,
				flashDelay: <?php echo isset($config['btn_flash_delay']) && !empty($config['btn_flash_delay']) ? esc_html($config['btn_flash_delay']) : '0'; ?>,
				planPicker: {
					disableGrouping: <?php echo isset($config['billing_cycle_group']) && $config['billing_cycle_group'] == 1 ? 'false' : 'true'; ?>,
					displayAll: <?php echo isset($config['hide_all_group']) && $config['hide_all_group'] == 1 ? 'false' : 'true'; ?>,
					expanded: <?php echo isset($config['expanded_plan_card']) && $config['expanded_plan_card'] != 1 ? 'false' : 'true'; ?>,
					collapsable: <?php echo isset($config['plan_card_collapse']) && $config['plan_card_collapse'] == 1 ? 'false' : 'true'; ?>,
					sortable: <?php echo isset($config['plan_card_sort']) && $config['plan_card_sort'] == 1 ? 'false' : 'true'; ?>,
					defaultSorting: '<?php echo isset($config['plan_card_defaultSort']) ? esc_js($config['plan_card_defaultSort']) : 'price:ascending'; ?>'
				}
			},
			<?php if (isset($config['mobile_config']) && $config['mobile_config'] == 'different') { ?>
				{
					shape: '<?php echo isset($config['mob_config_shape']) ? esc_js($config['mob_config_shape']) : 'rectangle'; ?>',
					icon: '<?php echo isset($config['mob_config_icon_code']) ? esc_js($config['mob_config_icon_code']) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAB6VBMVEUAAAD4t0j3tUf3tUf3tUf/uUbztkn3tUf3tUj4tEj3tUj4tkb3tUf5t0j2tEb3tUb3tUb3tUf3tUf4tkf3tUb3tEj1s0f//wD2tkn4tEf3tUf3tUf2tEf3tUb0tUr3tEf3tUf3tUf4tUb4tEf3tUf3skb2tEf3tUf2tUj3tUf3tUf3tEf3tEf3tUf3tUf4tEj3tUb3tUf3tUf/qlX2tUf2s0f3t0j3tUf5tkn2tUf3tkf3tEf4tEj2tEb3tUf2s0z4tkb2tUf3tUbvr0D2tUf3tUf4tUf2tUj2tUf0tUX3tUf4tUb4tUf/u0T2tEf4tEbxuEf3tkf3tUf3tkb2tUf2tUb3tUf/s033tUb3tEf/gID2tkf4tkj3tEf4tUf3t0j3tUf3tUj4t0j/zDP3tEf1tUX2tUb3tUf5tUf3tUf3tkf/qlX3tUf3tUf2tkj3tUb3tUf5tET3tkn3s0T2tUb2tkn4tET3tkb3tkj3tkb4tEf3tUj/qlX4tUf3tUf/tkn3tkf2tkf5tEb3tkfwtEv5uEf3tUf3tEj2tkX3tkf4tUj3tUj3tUf/tkn1tEb/v0D2tUf4tUf3tUf3tEf3tEfyrkP1tkb4tUf3tUf4uEf4tUf3tUb2tUf3tEf3tEb4tkf3tUf3tUcAAAA2J5auAAAAoXRSTlMAStnyogsV9IdHxE3JLnScwOnxqoZjMgEcjN/ikz4YlvjFRUToIXD9WWH+gj3746v27fwD0DZD0yrRZfNOOrsbSZSYEJDhznJWMPmKiQ93bRJetmKzkZ8Kx4UCsYt+SCCpvScFnTSV2E+l5Qlk3pJ/yCk/Hjc4IkKgp0t5BmimB7hzLIQRK7dcO7+O3dwOMwRTz+fbmhNQr+skjeTv1HvN8NO23wcAAAABYktHRACIBR1IAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAB3RJTUUH5QICCjsnzrtk/wAAAnRJREFUWMPl1vlbEkEYB/C3iKgMD6xWIxMrUwlFxOxQsIPEtO1SKpHosNJSu83ssrK7zO7bav7TdtxhW9xhdmb4pefp/WVZ2O/n2X13ZhgAZi1Y6FjkBPla7EIILckDWKrl0bI8gAIMLP9/AXdhUXEJBjylK1auUgTTZeWrUVZ511QIxNdW+pC1qtbx5tdvQPQqrubKb6xBucpTy5Gv8yNGbbLNB+qNixuCQXxoDJmApjKbfLiZXOkv3QzQgj9tUbYWbjMEu05uJ9e1tuEzHcCfIlHittsAVeROWyAbANjRgE922uTbyRvYBRYAdsf2dMTDNkAneYK9FICr4nq+S5EFunUgBLLAPtJrVRbYT3pwQBZoI4DDLQkczAxkzyFFCoDDxpht7lFlgArTxPH1JsQBOGKZwYLA0WPzAX9HXyQpIDj7KeuIN+XmF8LHG2lrUTTNv7SfOEldFk+d5r+L6vIQRfDFBVoxkD5TYiViIt2E5Nkii3BOBCB/rtnVKQ4MDp1v/QtcGBYGtJE4PDJqCCMygEZcNMaDHABwKfMuBySB5GUiXJEEoI4AV2UBNwHSssA1AlyXBcYIcIMZGb85cSsHcFvPu+6w8nfx9uSeSgMmvTpwn5VXXXPXjAYoQIw8wQMWEMgMt/6H8wBlivzieMQCHhsjvubJU6cJeGYstd3MFipR08R1PU+9wMeXtalB48tXKhOA6V7ELN9rsKmZviZGvuuNXV6rsbc58+/4NtzJnvfUeP2Hj1x5THz6bNmuf0l85Y3r3Sz4Fv1O2vFjNvgzMiMUz9SQeS7IlMQG418D5jY8v/IApn9rQCIPAMZnPRPs6fMHubZo52ygcAYAAAAldEVYdGRhdGU6Y3JlYXRlADIwMjEtMDItMDJUMTA6NTk6MzkrMDA6MDDj+gVuAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIxLTAyLTAyVDEwOjU5OjM5KzAwOjAwkqe90gAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAAASUVORK5CYII='; ?>',
					text: '<?php echo isset($config['mob_button_text']) ? esc_js($config['mob_button_text']) : __('Subscribe', 'sbis'); ?>',
					textColor: '<?php echo isset($config['mob_text_color']) ? esc_js($config['mob_text_color']) : '#f7b547'; ?>',
					backgroundColor: '<?php echo isset($config['mob_background_color']) ? esc_js($config['mob_background_color']) : '#20407d'; ?>',
					position: '<?php echo isset($config['mob_config_botton_position']) ? esc_js($config['mob_config_botton_position']) : 'right'; ?>',
					alignment: '<?php echo isset($config['mob_config_botton_alignment']) ? esc_js($config['mob_config_botton_alignment']) : 'center'; ?>',
					uprightText: <?php echo isset($config['mob_up_right_text']) && $config['mob_up_right_text'] != 1 ? 'false' : 'true'; ?>,
					showDelay: <?php echo isset($config['mob_btn_show_delay']) && !empty($config['mob_btn_show_delay']) ? esc_html($config['mob_btn_show_delay']) : '0'; ?>,
					flashDelay: <?php echo isset($config['mob_btn_flash_delay']) && !empty($config['mob_btn_flash_delay']) ? esc_html($config['mob_btn_flash_delay']) : '0'; ?>,
					planPicker: {
						disableGrouping: <?php echo isset($config['mob_billing_cycle_group']) && $config['mob_billing_cycle_group'] == 1 ? 'false' : 'true'; ?>,
						displayAll: <?php echo isset($config['mob_hide_all_group']) && $config['mob_hide_all_group'] == 1 ? 'false' : 'true'; ?>,
						expanded: <?php echo isset($config['mob_expanded_plan_card']) && $config['mob_expanded_plan_card'] != 1 ? 'false' : 'true'; ?>,
						collapsable: <?php echo isset($config['mob_plan_card_collapse']) && $config['mob_plan_card_collapse'] == 1 ? 'false' : 'true'; ?>,
						sortable: <?php echo isset($config['mob_plan_card_sort']) && $config['mob_plan_card_sort'] == 1 ? 'false' : 'true'; ?>,
						defaultSorting: '<?php echo isset($config['mob_plan_card_defaultSort']) ? esc_js($config['mob_plan_card_defaultSort']) : 'price:ascending'; ?>'
					}
				}
			<?php } else if (isset($config['mobile_config']) && $config['mobile_config'] == 'hide') { ?>true<?php } else { ?>undefined<?php } ?>);
			<?php } ?>
    </script>
	<?php } ?>
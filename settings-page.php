<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; //prevent direct access to this file
}
$sbis_values = get_option( 'sbse_embed_values' );
$sbis_notice = get_transient( 'sbis_result_' . get_current_user_id() );
$sbis_events = array(
	'click'      => __( 'Click', 'sbis' ),
	'dblclick'   => __( 'Double Click', 'sbis' ),
	'mousedown'  => __( 'Mousedown', 'sbis' ),
	'mouseup'    => __( 'Mouse up', 'sbis' ),
	'mouseenter' => __( 'Mouse Enter', 'sbis' ),
	'mouseout'   => __( 'Mouse Out', 'sbis' ),
	'mouseleave' => __( 'Mouse Leave', 'sbis' ),
	'mousemove'  => __( 'Mouse Move', 'sbis' ),
	'mouseover'  => __( 'Mouse Over', 'sbis' ),
);
?>
<h2>
	<?php esc_html_e( 'SubsBase Integration Settings', 'sbis' ) ?>
</h2>
<?php if ( $sbis_notice ) { ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html( $sbis_notice['value'] ); ?></p>
    </div>
<?php } ?>

<div id="sbse-settings-form" class="sbse-settings-form">
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="sbis_settings">
		<?php wp_nonce_field() ?>
        <div id="sbis-tabs">
            <ul class="nav-tab-wrapper">
                <li><a href="#tabs-1" class="nav-tab"><?php esc_html_e( 'Main Settings', 'sbis' ); ?></a></li>
                <li><a href="#tabs-2" class="nav-tab"><?php esc_html_e( 'Plan Integration', 'sbis' ); ?></a></li>
                <li><a href="#tabs-3" class="nav-tab"><?php esc_html_e( 'Plan Picker Integration', 'sbis' ); ?></a></li>
                <li><a href="#tabs-4" class="nav-tab"><?php esc_html_e( 'Advanced Settings', 'sbis' ); ?></a></li>
            </ul>
            <div id="tabs-1">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="site-id"><?php esc_html_e( 'Site ID', 'sbis' ); ?></label>
                        </th>
                        <td>
                            <input name="site_id" type="text" id="site-id" class="regular-text" required
                                   value="<?php echo isset( $sbis_values['site_id'] ) ? esc_attr( $sbis_values['site_id'] ) : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="checkout-version"><?php esc_html_e( 'Checkout Version', 'sbis' ); ?></label>
                        </th>
                        <td>
                            <input name="checkout_version" type="text" id="checkout-version" class="regular-text" disabled
                                   value="<?php echo esc_attr( $sbis_values['checkout_version'] ) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="color-scheme"><?php esc_html_e( 'Theme', 'sbis' ); ?></label>
                        </th>
                        <td>
                            <input name="color_scheme" type="text" id="color-scheme"
                                   class="sbis-color-picker regular-text"
                                   value="<?php echo isset( $sbis_values['color_scheme'] ) ? esc_attr( $sbis_values['color_scheme'] ) : '' ?>">
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-2">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="attach_checkout"><?php esc_html_e( 'Attach Checkout', 'sbis' ); ?></label>
                        </th>
                        <td>
                            <input name="attach_checkout" type="checkbox"
                                   id="attach_checkout" <?php echo isset( $sbis_values['attach_checkout'] ) && $sbis_values['attach_checkout'] == 1 ? 'checked' : '' ?>
                                   class="regular-text"
                                   value="1">
                        </td>
                    </tr>
                </table>

                <div class="attached-checkout">
                    <hr>
                    <div class="attached-checkout-box repeater">
                        <div class="repeater-checkout" data-repeater-list="checkout">
							<?php
							if ( isset( $sbis_values['checkout'] ) && is_array( $sbis_values['checkout'] ) && ! empty( $sbis_values['checkout'] ) ) {
								foreach ( $sbis_values['checkout'] as $checkout ) {
									?>
                                    <div class="repeater-item" data-repeater-item>
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <label><?php esc_html_e( 'Plan Code', 'sbis' ); ?></label>
                                                    <br>
                                                    <br>
                                                    <input name="plan_code" type="text"
                                                        value="<?php echo isset( $checkout['plan_code'] ) ? esc_attr( $checkout['plan_code'] ) : '' ?>">
                                                </td>
                                                <td>
                                                    <label><?php esc_html_e( 'Selector', 'sbis' ); ?></label>
                                                    <br>
                                                    <br>
                                                    <input name="selector" type="text"
                                                        value="<?php echo isset( $checkout['selector'] ) ? esc_attr( $checkout['selector'] ) : '' ?>">
                                                </td>
                                                <td>
													<?php esc_html_e( 'Selector Type', 'sbis' ); ?>
                                                    <br>
                                                    <br>
                                                    <fieldset>
                                                        <p>
                                                            <label>
                                                                <input name="selector_type" type="radio" value="class"
																	<?php echo isset( $checkout['selector_type'] ) && $checkout['selector_type'] == 'class' ? 'checked' : '' ?>>
                                                                Class
                                                                </label>
                                                            <label>
                                                                <input name="selector_type" type="radio" value="id"
                                                                    <?php echo (isset( $checkout['selector_type'] ) && $checkout['selector_type'] == 'id') || !isset( $checkout['selector_type'] ) ? 'checked' : '' ?>>
                                                                ID
                                                            </label>
                                                        </p>
                                                    </fieldset>
                                                </td>
                                                <td>
                                                    <label><?php esc_html_e( 'Event', 'sbis' ); ?></label>
                                                    <br>
                                                    <br>

                                                    <select name="event">
														<?php foreach ( $sbis_events as $event_key => $event_value ) {
															?>
                                                            <option value="<?php echo esc_attr( $event_key ) ?>" <?php
															if ( isset( $checkout['event'] ) && $checkout['event'] == $event_key ) {
																echo 'selected';
															}
															?>>
																<?php echo esc_html( $event_value ) ?>
                                                            </option>
															<?php
														} ?>
                                                    </select>

                                                </td>
                                            </tr>
                                        </table>
                                        <button type="button" class="delete-me" title="<?php esc_html_e( 'Remove' ); ?>"
                                                data-repeater-delete>
                                            X
                                        </button>
                                    </div>
									<?php
								}
							} else {
								?>
                                <div class="repeater-item" data-repeater-item>
                                    <table class="form-table">
                                        <tr>
                                            <td>
                                                <label><?php esc_html_e( 'Plan Code', 'sbis' ); ?></label>
                                                <br>
                                                <br>
                                                <input name="plan_code" type="text">
                                            </td>
                                            <td>
                                                <label><?php esc_html_e( 'Selector', 'sbis' ); ?></label>
                                                <br>
                                                <br>
                                                <input name="selector" type="text">
                                            </td>
                                            <td>
												<?php esc_html_e( 'Selector Type', 'sbis' ); ?>
                                                <br>
                                                <br>
                                                <fieldset>
                                                    <p>
                                                        <label>
                                                            <input name="selector_type" type="radio" value="class">
                                                            Class
                                                        </label>
                                                        <label>
                                                            <input name="selector_type" type="radio"value="id" checked>
                                                            ID
                                                        </label>
                                                    </p>
                                                </fieldset>
                                            </td>
                                            <td>
                                                <label><?php esc_html_e( 'Event', 'sbis' ); ?></label>
                                                <br>
                                                <br>
                                                <input name="event" type="text">
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="delete-me" title="<?php esc_html_e( 'Remove' ); ?>"
                                            data-repeater-delete>
                                        X
                                    </button>
                                </div>
								<?php
							}
							?>

                        </div>
                        <p>
                            <button type="button" class="button add-more-btn button-primary" data-repeater-create>
								<?php esc_html_e( 'Add More', 'sbis' ) ?>
                            </button>
                        </p>
                    </div>

                    <h3>
						<?php esc_html_e( 'Query Parameters', 'sbis' ) ?>
                    </h3>
                    <div class="query-parameters repeater">
                        <h4>
							<?php esc_html_e( 'Info Fields', 'sbis' ) ?>
                        </h4>
                        <div class="repeater-checkout" data-repeater-list="parameters">
							<?php
							if ( isset( $sbis_values['parameters'] ) && is_array( $sbis_values['parameters'] ) && ! empty( $sbis_values['parameters'] ) ) {
								foreach ( $sbis_values['parameters'] as $parameter ) {
									?>
                                    <div class="repeater-item" data-repeater-item>
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <label><?php esc_html_e( 'Key', 'sbis' ); ?></label>
                                                    <input name="key" type="text" class="regular-text"
                                                           value="<?php echo isset( $parameter['key'] ) ? esc_attr( $parameter['key'] ) : '' ?>">
                                                </td>

                                                <td>
                                                    <label><?php esc_html_e( 'Value', 'sbis' ); ?></label>
                                                    <input name="value" type="text" class="regular-text"
                                                           value="<?php echo isset( $parameter['value'] ) ? esc_attr( $parameter['value'] ) : '' ?>">
                                                </td>
                                            </tr>
                                        </table>
                                        <button type="button" class="delete-me" title="<?php esc_html_e( 'Remove' ); ?>"
                                                data-repeater-delete>
                                            X
                                        </button>
                                    </div>
									<?php
								}
							} else {
								?>
                                <div class="repeater-item" data-repeater-item>
                                    <table class="form-table">
                                        <tr>
                                            <td>
                                                <label><?php esc_html_e( 'Key', 'sbis' ); ?></label>
                                                <input name="key" type="text" class="regular-text"
                                                       value="">
                                            </td>
                                            <td>
                                                <label><?php esc_html_e( 'Value', 'sbis' ); ?></label>
                                                <input name="value" type="text" class="regular-text"
                                                       value="">
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="delete-me" title="<?php esc_html_e( 'Remove' ); ?>"
                                            data-repeater-delete>
                                        X
                                    </button>
                                </div>
								<?php
							}
							?>
                        </div>
                        <p>
                            <button type="button" class="button add-more-btn button-primary" data-repeater-create>
								<?php esc_html_e( 'Add More', 'sbis' ) ?>
                            </button>
                        </p>
                    </div>

                    <div class="info-parameters repeater">
                        <h4>
							<?php esc_html_e( 'Custom Fields', 'sbis' ) ?>
                        </h4>
                        <div class="repeater-checkout" data-repeater-list="info">
							<?php
							if ( isset( $sbis_values['custom_fields'] ) && is_array( $sbis_values['custom_fields'] ) && ! empty( $sbis_values['custom_fields'] ) ) {
								foreach ( $sbis_values['custom_fields'] as $field ) {
									?>
                                    <div class="repeater-item" data-repeater-item>
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <label><?php esc_html_e( 'Key', 'sbis' ); ?></label>
                                                    <input name="key" type="text" class="regular-text"
                                                           value="<?php echo isset( $field['key'] ) ? esc_attr( $field['key'] ) : '' ?>">
                                                </td>
                                                <td>
                                                    <label><?php esc_html_e( 'Value', 'sbis' ); ?></label>
                                                    <input name="value" type="text" class="regular-text"
                                                           value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : '' ?>">
                                                </td>
                                            </tr>
                                        </table>
                                        <button type="button" class="delete-me" title="<?php esc_html_e( 'Remove' ); ?>"
                                                data-repeater-delete>
                                            X
                                        </button>
                                    </div>
									<?php
								}
							} else {
								?>
                                <div class="repeater-item" data-repeater-item>
                                    <table class="form-table">
                                        <tr>
                                            <td>
                                                <label><?php esc_html_e( 'Key', 'sbis' ); ?></label>
                                                <input name="key" type="text" class="regular-text"
                                                       value="">
                                            </td>
                                            <td>
                                                <label><?php esc_html_e( 'Value', 'sbis' ); ?></label>
                                                <input name="value" type="text" class="regular-text"
                                                       value="">
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="delete-me" title="<?php esc_html_e( 'Remove' ); ?>"
                                            data-repeater-delete>
                                        X
                                    </button>
                                </div>
								<?php
							}
							?>
                        </div>
                        <p>
                            <button type="button" class="button add-more-btn button-primary" data-repeater-create>
								<?php esc_html_e( 'Add More', 'sbis' ) ?>
                            </button>
                        </p>
                    </div>
                </div>

            </div>


            <div id="tabs-3">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="attach_plan_picker"><?php esc_html_e( 'Attach Plan Picker', 'sbis' ) ?></label>
                        </th>
                        <td>
                            <input name="attach_plan_picker" type="checkbox" id="attach_plan_picker"
                                   class="regular-text" <?php echo isset( $sbis_values['attach_plan_picker'] ) && $sbis_values['attach_plan_picker'] == 1 ? 'checked' : '' ?>
                                   value="1">
                        </td>
                    </tr>
                </table>
                <div class="plan-picker">
                    <hr>
					<?php esc_html_e( 'Plan Picker Settings', 'sbis' ) ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="billing_cycle_group"><?php esc_html_e( 'Group By Billing Cycle', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="billing_cycle_group" type="checkbox" id="billing_cycle_group" 
                                <?php
								    if ( !isset( $sbis_values['billing_cycle_group'] ) ) {
									echo 'checked';
								    }
								    echo isset( $sbis_values['billing_cycle_group'] ) && $sbis_values['billing_cycle_group'] == 1 ? 'checked' : '' 
                                ?>
                                value="1">
                            </td>
                        </tr>
                        <tr id="related_to_billing_cycle_group">
                            <th scope="row">
                                <label for="hide_all_group"><?php esc_html_e( 'Hide the "All" Group Button', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="hide_all_group" type="checkbox" id="hide_all_group" 
                                <?php
								    echo isset( $sbis_values['hide_all_group'] ) && $sbis_values['hide_all_group'] == 1 ? 'checked' : '' 
                                ?>
                                value="0">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="expanded_plan_card"><?php esc_html_e( 'Expand Plans By Default', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="expanded_plan_card" type="checkbox" id="expanded_plan_card" 
                                <?php
								    echo isset( $sbis_values['expanded_plan_card'] ) && $sbis_values['expanded_plan_card'] == 1 ? 'checked' : '' 
                                ?>
                                value="0">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="plan_card_collapse"><?php esc_html_e( 'Disable Plan Collapsing/Expanding', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="plan_card_collapse" type="checkbox" id="plan_card_collapse" 
                                <?php
								    echo isset( $sbis_values['plan_card_collapse'] ) && $sbis_values['plan_card_collapse'] == 1 ? 'checked' : '' 
                                ?>
                                value="0">
                            </td>
                        </tr>
                        <tr>
                                <th scope="row">
                                    <label for="plan_card_sort"><?php esc_html_e( 'Disable Plans Sorting', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="plan_card_sort" type="checkbox" id="plan_card_sort" 
                                    <?php
                                        echo isset( $sbis_values['plan_card_sort'] ) && $sbis_values['plan_card_sort'] == 1 ? 'checked' : '' 
                                    ?>
                                    value="0">
                                </td>
                            </tr>
                            <tr id="plan_card_defaultSort_row">
                                <th scope="row"><label><?php esc_html_e( 'Default Sorting', 'sbis' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <p>
                                            <label><input name="plan_card_defaultSort" type="radio" value="price:ascending"
                                                    <?php
                                                    if ( ! isset( $sbis_values['plan_card_defaultSort'] ) ) {
                                                        echo 'checked';
                                                    }
                                                    echo isset( $sbis_values['plan_card_defaultSort'] ) && $sbis_values['plan_card_defaultSort'] == '"price:ascending' ? 'checked' : '' ?>><?php esc_html_e( 'Price: Low to High', 'sbis' ); ?>
                                            </label>
                                            <br>
                                            <label><input name="plan_card_defaultSort"
                                                          type="radio" <?php echo isset( $sbis_values['plan_card_defaultSort'] ) && $sbis_values['plan_card_defaultSort'] == 'price:descending' ? 'checked' : '' ?>
                                                          value="price:descending"><?php esc_html_e( 'Price: High to Low', 'sbis' ); ?></label>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                    </table>
                    <hr>
					<?php esc_html_e( 'Button Settings', 'sbis' ) ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Shape', 'sbis' ); ?></label></th>
                            <td>
                                <fieldset>
                                    <p>
                                        <label><input name="config_shape" type="radio" value="rectangle"
												<?php
												if ( ! isset( $sbis_values['config_shape'] ) ) {
													echo 'checked';
												}
												echo isset( $sbis_values['config_shape'] ) && $sbis_values['config_shape'] == 'rectangle' ? 'checked' : '' ?>><?php esc_html_e( 'Rectangle', 'sbis' ); ?>
                                        </label>
                                        <label><input name="config_shape"
                                                      type="radio" <?php echo isset( $sbis_values['config_shape'] ) && $sbis_values['config_shape'] == 'circle' ? 'checked' : '' ?>
                                                      value="circle"><?php esc_html_e( 'Circle', 'sbis' ); ?></label>
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                        <tr class="sbis-related-to-circle">
                            <th scope="row">
                                <label for="config_icon_code"><?php esc_html_e( 'Icon', 'sbis' ); ?></label>
                            </th>
                            <td>
                        <textarea name="config_icon_code" id="config_icon_code" class="regular-text"
                                  rows="10"><?php echo isset( $sbis_values['config_icon_code'] ) ? esc_html__( $sbis_values['config_icon_code'] ) : 'data:image/png;base64,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' ?></textarea>
                                <p class="description"><?php
									echo sprintf( __( 'You can use this %s %s %s to convert your image', 'sbis' ), '<a target="_blank" href="https://www.base64-image.de">', __( 'link', 'sbis' ), '</a>' );
									?></p>
                            </td>
                        </tr>

                        <tr class="sbis-related-to-rectangle">
                            <th scope="row">
                                <label for="button_text"><?php esc_html_e( 'Button Text', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="button_text" type="text" id="button_text" class="regular-text"
                                       value="<?php echo isset( $sbis_values['button_text'] ) ? esc_attr( $sbis_values['button_text'] ) : 'Subscribe' ?>">
                            </td>
                        </tr>

                        <tr class="sbis-related-to-rectangle">
                            <th scope="row">
                                <label for="text_color"><?php esc_html_e( 'Text Color', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="text_color" type="text" id="text_color"
                                       class="sbis-color-picker regular-text"
                                       value="<?php echo isset( $sbis_values['text_color'] ) ? esc_attr( $sbis_values['text_color'] ) : '#f7b547' ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="background_color"><?php esc_html_e( 'Background Color', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="background_color" type="text" id="background_color"
                                       class="sbis-color-picker regular-text"
                                       value="<?php echo isset( $sbis_values['background_color'] ) ? esc_attr( $sbis_values['background_color'] ) : '#20407d' ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Button Position', 'sbis' ); ?></label></th>
                            <td>
                                <fieldset>
                                    <p>
                                        <label><input name="config_botton_position" type="radio"
                                                      value="right" <?php if ( ! isset( $sbis_values['config_botton_position'] ) ) {
												echo 'checked';
											}
											echo isset( $sbis_values['config_botton_position'] ) && $sbis_values['config_botton_position'] == 'right' ? 'checked' : '' ?>><?php esc_html_e( 'Right', 'sbis' ); ?>
                                        </label>
                                        <label><input name="config_botton_position"
                                                      type="radio" <?php echo isset( $sbis_values['config_botton_position'] ) && $sbis_values['config_botton_position'] == 'left' ? 'checked' : '' ?>
                                                      value="left"><?php esc_html_e( 'Left', 'sbis' ); ?></label>
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Button Alignment', 'sbis' ); ?></label></th>
                            <td>
                                <fieldset>
                                    <p>
                                        <label><input name="config_botton_alignment" type="radio"
                                                      value="top" <?php echo isset( $sbis_values['config_botton_alignment'] ) && $sbis_values['config_botton_alignment'] == 'top' ? 'checked' : '' ?>>
											<?php esc_html_e( 'Top', 'sbis' ); ?></label>
                                        <label><input name="config_botton_alignment" type="radio"
                                                      value="center" <?php if ( ! isset( $sbis_values['config_botton_alignment'] ) ) {
												echo 'checked';
											}
											echo isset( $sbis_values['config_botton_alignment'] ) && $sbis_values['config_botton_alignment'] == 'center' ? 'checked' : '' ?>><?php esc_html_e( 'Center', 'sbis' ); ?>
                                        </label>
                                        <label><input name="config_botton_alignment"
                                                      type="radio" <?php echo isset( $sbis_values['config_botton_alignment'] ) && $sbis_values['config_botton_alignment'] == 'bottom' ? 'checked' : '' ?>
                                                      value="bottom"><?php esc_html_e( 'Bottom', 'sbis' ); ?></label>
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                        <tr class="sbis-related-to-rectangle">
                            <th scope="row">
                                <label for="up_right_text"><?php esc_html_e( 'Upright text', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="up_right_text" type="checkbox"
                                       id="up_right_text" <?php
								if ( ! isset( $sbis_values['up_right_text'] ) ) {
									echo 'checked';
								}
								echo isset( $sbis_values['up_right_text'] ) && $sbis_values['up_right_text'] == 1 ? 'checked' : '' ?>
                                       value="1">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="btn_show_delay"><?php esc_html_e( 'Button Show Delay', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="btn_show_delay" type="number" id="btn_show_delay" min="0" step="1"
                                       value="<?php echo isset( $sbis_values['btn_show_delay'] ) ? esc_attr( $sbis_values['btn_show_delay'] ) : '0' ?>"><small><i><?php esc_html_e( 'In Seconds', 'sbis' ); ?></i></small>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="btn_flash_delay"><?php esc_html_e( 'Button Flash Delay', 'sbis' ); ?></label>
                            </th>
                            <td>
                                <input name="btn_flash_delay" type="number" id="btn_flash_delay" min="0" step="1"
                                       value="<?php echo isset( $sbis_values['btn_flash_delay'] ) ? esc_attr( $sbis_values['btn_flash_delay'] ) : '0' ?>"><small><i><?php esc_html_e( 'In Seconds', 'sbis' ); ?></i></small>
                            </td>
                        </tr>

                    </table>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Mobile Devices Configuration', 'sbis' ); ?></label></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input name="mobile_config" type="radio" value="same" <?php if ( ! isset( $sbis_values['mobile_config'] ) ) { echo 'checked';} echo isset( $sbis_values['mobile_config'] ) && $sbis_values['mobile_config'] == 'same' ? 'checked' : '' ?>>
                                        <?php esc_html_e( 'Same as above configuration', 'sbis' ); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input name="mobile_config" type="radio" value="hide" <?php echo isset( $sbis_values['mobile_config'] ) && $sbis_values['mobile_config'] == 'hide' ? 'checked' : '' ?>>
                                        <?php esc_html_e( 'Hide on mobile devices', 'sbis' ); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input name="mobile_config" type="radio" value="different" <?php echo isset( $sbis_values['mobile_config'] ) && $sbis_values['mobile_config'] == 'different' ? 'checked' : '' ?>>
                                        <?php esc_html_e( 'Customized configuration', 'sbis' ); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                    <div id="plan-picker-mobile">
                        <hr>
					    <?php esc_html_e( 'Plan Picker Settings on Mobile Devices', 'sbis' ) ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="mob_billing_cycle_group"><?php esc_html_e( 'Group By Billing Cycle', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_billing_cycle_group" type="checkbox" id="mob_billing_cycle_group" 
                                    <?php
                                        if ( !isset( $sbis_values['mob_billing_cycle_group'] ) ) {
                                        echo 'checked';
                                        }
                                        echo isset( $sbis_values['mob_billing_cycle_group'] ) && $sbis_values['mob_billing_cycle_group'] == 1 ? 'checked' : '' 
                                    ?>
                                    value="1">
                                </td>
                            </tr>
                            <tr id="mob_related_to_billing_cycle_group">
                                <th scope="row">
                                    <label for="mob_hide_all_group"><?php esc_html_e( 'Hide the "All" Group Button', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_hide_all_group" type="checkbox" id="mob_hide_all_group" 
                                    <?php
                                        echo isset( $sbis_values['mob_hide_all_group'] ) && $sbis_values['mob_hide_all_group'] == 1 ? 'checked' : '' 
                                    ?>
                                    value="0">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="mob_expanded_plan_card"><?php esc_html_e( 'Expand Plans By Default', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_expanded_plan_card" type="checkbox" id="mob_expanded_plan_card" 
                                    <?php
                                        echo isset( $sbis_values['mob_expanded_plan_card'] ) && $sbis_values['mob_expanded_plan_card'] == 1 ? 'checked' : '' 
                                    ?>
                                    value="0">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="mob_plan_card_collapse"><?php esc_html_e( 'Disable Plan Collapsing/Expanding', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_plan_card_collapse" type="checkbox" id="mob_plan_card_collapse" 
                                    <?php
                                        echo isset( $sbis_values['mob_plan_card_collapse'] ) && $sbis_values['mob_plan_card_collapse'] == 1 ? 'checked' : '' 
                                    ?>
                                    value="0">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="mob_plan_card_sort"><?php esc_html_e( 'Disable Plans Sorting', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_plan_card_sort" type="checkbox" id="mob_plan_card_sort" 
                                    <?php
                                        echo isset( $sbis_values['mob_plan_card_sort'] ) && $sbis_values['mob_plan_card_sort'] == 1 ? 'checked' : '' 
                                    ?>
                                    value="0">
                                </td>
                            </tr>
                            <tr id="mob_plan_card_defaultSort_row">
                                <th scope="row"><label><?php esc_html_e( 'Default Sorting', 'sbis' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <p>
                                            <label>
                                                <input name="mob_plan_card_defaultSort" type="radio" value="price:ascending"
                                                    <?php
                                                    if ( ! isset( $sbis_values['mob_plan_card_defaultSort'] ) ) {
                                                        echo 'checked';
                                                    }
                                                    echo isset( $sbis_values['mob_plan_card_defaultSort'] ) && $sbis_values['mob_plan_card_defaultSort'] == '"price:ascending' ? 'checked' : '' ?>><?php esc_html_e( 'Price: Low to High', 'sbis' ); ?>
                                            </label>
                                            <br>
                                            <label>
                                                <input name="mob_plan_card_defaultSort" type="radio" value="price:descending"
                                                    <?php echo isset( $sbis_values['mob_plan_card_defaultSort'] ) && $sbis_values['mob_plan_card_defaultSort'] == 'price:descending' ? 'checked' : '' ?>
                                                    ><?php esc_html_e( 'Price: High to Low', 'sbis' ); ?>
                                            </label>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <?php esc_html_e( 'Button Settings on Mobile Devices', 'sbis' ) ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label><?php esc_html_e( 'Shape', 'sbis' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <p>
                                            <label><input name="mob_config_shape" type="radio" value="rectangle"
                                                    <?php
                                                    if ( ! isset( $sbis_values['mob_config_shape'] ) ) {
                                                        echo 'checked';
                                                    }
                                                    echo isset( $sbis_values['mob_config_shape'] ) && $sbis_values['mob_config_shape'] == 'rectangle' ? 'checked' : '' ?>><?php esc_html_e( 'Rectangle', 'sbis' ); ?>
                                            </label>
                                            <label><input name="mob_config_shape"
                                                          type="radio" <?php echo isset( $sbis_values['mob_config_shape'] ) && $sbis_values['mob_config_shape'] == 'circle' ? 'checked' : '' ?>
                                                          value="circle"><?php esc_html_e( 'Circle', 'sbis' ); ?></label>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr class="sbis-related-to-mob-circle">
                                <th scope="row">
                                    <label for="config_icon_code"><?php esc_html_e( 'Icon', 'sbis' ); ?></label>
                                </th>
                                <td>
                        <textarea name="mob_config_icon_code" id="mob_config_icon_code" class="regular-text"
                                  rows="10"><?php echo isset( $sbis_values['mob_config_icon_code'] ) ? esc_html__( $sbis_values['mob_config_icon_code'] ) : 'data:image/png;base64,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' ?></textarea>
                                    <p class="description"><?php
                                        echo sprintf( __( 'You can use this %s %s %s to convert your image', 'sbis' ), '<a target="_blank" href="https://www.base64-image.de">', __( 'link', 'sbis' ), '</a>' );
                                        ?></p>
                                </td>
                            </tr>

                            <tr class="sbis-related-to-mob-rectangle">
                                <th scope="row">
                                    <label for="mob_button_text"><?php esc_html_e( 'Button Text', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_button_text" type="text" id="mob_button_text" class="regular-text"
                                           value="<?php echo isset( $sbis_values['mob_button_text'] ) ? esc_attr( $sbis_values['mob_button_text'] ) : 'Subscribe' ?>">
                                </td>
                            </tr>

                            <tr class="sbis-related-to-mob-rectangle">
                                <th scope="row">
                                    <label for="mob_text_color"><?php esc_html_e( 'Text Color', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_text_color" type="text" id="mob_text_color"
                                           class="sbis-color-picker regular-text"
                                           value="<?php echo isset( $sbis_values['mob_text_color'] ) ? esc_attr( $sbis_values['mob_text_color'] ) : '#f7b547' ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="mob_background_color"><?php esc_html_e( 'Background Color', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_background_color" type="text" id="mob_background_color"
                                           class="sbis-color-picker regular-text"
                                           value="<?php echo isset( $sbis_values['mob_background_color'] ) ? esc_attr( $sbis_values['mob_background_color'] ) : '#20407d' ?>">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label><?php esc_html_e( 'Button Position', 'sbis' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <p>
                                            <label><input name="mob_config_botton_position" type="radio"
                                                          value="right" <?php if ( ! isset( $sbis_values['mob_config_botton_position'] ) ) {
                                                    echo 'checked';
                                                }
                                                echo isset( $sbis_values['mob_config_botton_position'] ) && $sbis_values['mob_config_botton_position'] == 'right' ? 'checked' : '' ?>><?php esc_html_e( 'Right', 'sbis' ); ?>
                                            </label>
                                            <label><input name="mob_config_botton_position"
                                                          type="radio" <?php echo isset( $sbis_values['mob_config_botton_position'] ) && $sbis_values['mob_config_botton_position'] == 'left' ? 'checked' : '' ?>
                                                          value="left"><?php esc_html_e( 'Left', 'sbis' ); ?></label>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label><?php esc_html_e( 'Button Alignment', 'sbis' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <p>
                                            <label><input name="mob_config_botton_alignment" type="radio"
                                                          value="top" <?php echo isset( $sbis_values['mob_config_botton_alignment'] ) && $sbis_values['mob_config_botton_alignment'] == 'top' ? 'checked' : '' ?>>
                                                <?php esc_html_e( 'Top', 'sbis' ); ?></label>
                                            <label><input name="mob_config_botton_alignment" type="radio"
                                                          value="center" <?php if ( ! isset( $sbis_values['config_botton_alignment'] ) ) {
                                                    echo 'checked';
                                                }
                                                echo isset( $sbis_values['mob_config_botton_alignment'] ) && $sbis_values['mob_config_botton_alignment'] == 'center' ? 'checked' : '' ?>><?php esc_html_e( 'Center', 'sbis' ); ?>
                                            </label>
                                            <label><input name="mob_config_botton_alignment"
                                                          type="radio" <?php echo isset( $sbis_values['mob_config_botton_alignment'] ) && $sbis_values['mob_config_botton_alignment'] == 'bottom' ? 'checked' : '' ?>
                                                          value="bottom"><?php esc_html_e( 'Bottom', 'sbis' ); ?></label>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr class="sbis-related-to-mob-rectangle">
                                <th scope="row">
                                    <label for="mob_up_right_text"><?php esc_html_e( 'Upright text', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_up_right_text" type="checkbox"
                                           id="mob_up_right_text" <?php
                                    if ( ! isset( $sbis_values['mob_up_right_text'] ) ) {
                                        echo 'checked';
                                    }
                                    echo isset( $sbis_values['mob_up_right_text'] ) && $sbis_values['mob_up_right_text'] == 1 ? 'checked' : '' ?>
                                           value="1">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="mob_btn_show_delay"><?php esc_html_e( 'Button Show Delay', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_btn_show_delay" type="number" id="mob_btn_show_delay" min="0" step="1"
                                           value="<?php echo isset( $sbis_values['mob_btn_show_delay'] ) ? esc_attr( $sbis_values['mob_btn_show_delay'] ) : '0' ?>"><small><i><?php esc_html_e( 'In Seconds', 'sbis' ); ?></i></small>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="mob_btn_flash_delay"><?php esc_html_e( 'Button Flash Delay', 'sbis' ); ?></label>
                                </th>
                                <td>
                                    <input name="mob_btn_flash_delay" type="number" id="mob_btn_flash_delay" min="0" step="1"
                                           value="<?php echo isset( $sbis_values['mob_btn_flash_delay'] ) ? esc_attr( $sbis_values['mob_btn_flash_delay'] ) : '0' ?>"><small><i><?php esc_html_e( 'In Seconds', 'sbis' ); ?></i></small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div id="tabs-4">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="attach_callback"><?php esc_html_e( 'Activate Callback', 'sbis' ); ?></label>
                        </th>
                        <td>
                            <input name="attach_callback" type="checkbox"
                                   id="attach_callback" <?php echo isset( $sbis_values['attach_callback'] ) && $sbis_values['attach_callback'] == 1 ? 'checked' : '' ?>
                                   value="1">
                        </td>
                    </tr>
                    <tr class="sbis-js-callback">
                        <th scope="row">
                            <label for="js-callback"><?php esc_html_e( 'Callback Function', 'sbis' ); ?></label>
                        </th>
                        <td>
                    <textarea name="js_callback" id="js-callback" class="regular-text"
                              rows="10"><?php echo $sbis_values['js_callback'] ?? '' ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="success_url"><?php esc_html_e( 'Success Redirect URL', 'sbis' ); ?></label>
                        </th>
                        <td colspan="3">
                            <input name="success_url" type="text" id="success_url" placeholder="https://mywebsite.com/success" pattern="https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)" size="60" value="<?php echo esc_attr( $sbis_values['success_url'] ) ?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
		<?php submit_button(); ?>
    </form>
</div>

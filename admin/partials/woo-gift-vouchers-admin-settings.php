<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://falconplugins.store
 * @since      1.0.0
 *
 * @package    Woo_Gift_Vouchers
 * @subpackage Woo_Gift_Vouchers/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Gift_Vouchers
 * @subpackage Woo_Gift_Vouchers/includes
 * @author     Falcon Plugins <plugins@falconplugins.store>
 */
class Woo_Gift_Vouchers_Admin_Settings {

    private $tab_id;

	public function __construct() {

        $this->tab_id = "wgv-settings";

        add_filter( 
            'plugin_action_links_woo-gift-vouchers/woo-gift-vouchers.php', 
            array(
                $this,
                'wgv_plugin_settings_link'
            )
        );

        add_filter( 
            'woocommerce_sections_' . $this->tab_id, 
            array( 
                $this,
                'wgv_add_setting_section'
            )
        );

        add_filter( 
            'woocommerce_settings_tabs_array', 
            array( 
                $this, 
                'wgv_woo_setting_tab' 
            ), 
            21 
        );

        add_action( 
            'woocommerce_settings_tabs_' . $this->tab_id, 
            array( 
                $this, 
                'wgv_woo_tab_content' 
            ) 
        );

        add_action( 
            'woocommerce_update_options_' . $this->tab_id, 
            array(
                $this,
                'wgv_update_settings'
            )
        );

	}

    public function wgv_plugin_settings_link( $links ) {
       
        $url = esc_url( admin_url( "admin.php?page=wc-settings&tab=wgv-settings" ) );
        
        // Create the link.
        $settings_link = "<a href='$url'>" . __( 'Settings', 'woo-gift-vouchers' ) . '</a>';
        
        // Adds the link to the end of the array.
        array_push(
            $links,
            $settings_link
        );

        return array_reverse( $links );
    }

    public function wgv_add_setting_section( $sections ) {

        global $current_section;

        $sections = array(
            ''  => __( 'General Settings', 'woo-gift-vouchers' ),
            'wgv-design-settings'   => 'Design Settings',
        );

        echo '<ul class="subsubsub">';

        foreach( $sections as $id => $label ) {
            $url = add_query_arg(
                array(
                    'page' => 'wc-settings',
                    'tab' => $this->tab_id,
                    'section' => $id,
                ),
                admin_url( 'admin.php' )
            );

            $current = $current_section == $id ? 'class="current"' : '';

            $separator = end( array_keys( $sections ) ) === $id ? '' : '|';

            echo "<li><a href=\"$url\" $current>$label</a> $separator </li>";
        }

        echo '</ul><br class="clear" />';

    }

    public function wgv_woo_setting_tab( $tabs ) {
        // position (after Products)
        $pos = 7;
        // new tab slug and title
        $new_tab = array( 
            'wgv-settings' => __( 'Woo Gift Voucher', 'woo-gift-vouchers' )
        );

        // add into an appropriate position of an existing array
        $tabs = array_slice( $tabs, 0, $pos, true ) + $new_tab + array_slice( $tabs, $pos, NULL, true );

        return $tabs;
    }

    public function wgv_woo_tab_content() {

        woocommerce_admin_fields( $this->wgv_settings_fields() );
        
    }

    public function wgv_settings_fields() {

        ob_start();
        ?>
            <div class="multiple_voucher_text_wrap">
                <p class="multiple_voucher_text">
                    <?php _e( "<h4>Do you want to send multiple gift vouchers on bases of Multiple?</h4>", "woo-gift-vouchers" ); ?>
                    <?php _e( "<strong>Example:</strong> The minimum order value is <strong>1000</strong> and Gift Voucher amount is <strong>500</strong>.", "woo-gift-vouchers" ); ?>
                    <br/>
                    <?php _e( "Check if you want to send <strong>500 x 2</strong> vouchers on order value of more than <strong>2000</strong>", "woo-gift-vouchers" ); ?>
                </p>
            </div>
        <?php
        $multiple_voucher_html = ob_get_clean();

		$general_settings = array(
			'section_title' => array(
                'name'     => __( 'WooCommerce Gift Voucher Settings :: General', 'woo-gift-vouchers' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_' . $this->tab_id . '_section_title',
            ),
            'wgv_enable' => array(
                'name' => __( 'Enable' ),
                'type' => 'checkbox',
                'id'    => 'wgv_enable',
            ),
            'product_types' => array(
                'name' => __( 'Product Types' ),
                'type' => 'multiselect',
                'desc' => __( 'Which product types should be eligible for vouchers.', 'woo-gift-vouchers' ),
                'desc_tip' => true,
                'id'    => 'wc_product_types',
                'multiple' => 'multiple',
                'options' => wc_get_product_types()
            ),
            'gift_amount' => array(
                'name' => __( 'Gift Amount', 'woo-gift-vouchers' ),
                'type' => 'number',
                'placeholder' => __( 'Gift Amount', 'woo-gift-vouchers' ),
                'desc' => __( 'Enter amount you want to set as a gift voucher.', 'woo-gift-vouchers' ),
                'id'   => 'wc_' . $this->tab_id . '_gift_amount'
            ),
            'minimum_order' => array(
                'name' => __( 'Minimum Order Value', 'woo-gift-vouchers' ),
                'type' => 'number',
                'placeholder' => __( 'Minimum Order Value', 'woo-gift-vouchers' ),
                'desc' => __( 'Set the minimum order value for applying gift voucher.', 'woo-gift-vouchers' ),
                'id'   => 'wc_' . $this->tab_id . '_minimum_order'
            ),
            'multiply_vouchers' => array(
                'name' => __( 'Multiple Vouchers' ),
                'type' => 'checkbox',
                'id'    => 'wc_multiply_vouchers',
                'desc' => $multiple_voucher_html,
            ),
            'maximum_vouchers' => array(
                'name' => __( 'Maximum Vouchers Per Order', 'woo-gift-vouchers' ),
                'type' => 'number',
                'placeholder' => __( 'Maximum Vouchers Per Order', 'woo-gift-vouchers' ),
                'desc' => __( 'Set the maximum vouchers a user could get per order. <strong>-1</strong> for unlimited.', 'woo-gift-vouchers' ),
                'id'   => 'wc_' . $this->tab_id . '_maximum_vouchers'
            ),
            'limit_user' => array(
                'name' => __( 'Limit Users?', 'woo-gift-vouchers' ),
                'type' => 'checkbox',
                'desc' => __( 'Make a user ineligible, if they have availed voucher(s) once.', 'woo-gift-vouchers' ),
                'id'   => 'wc_' . $this->tab_id . '_limit_user'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_' . $this->tab_id . '_section_end'
            )
		);

        $design_settings = array(
			'section_title' => array(
                'name'     => __( 'WooCommerce Gift Voucher Settings :: Design', 'woo-gift-vouchers' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_' . $this->tab_id . '__design_section_title',
            ),
            'wc_wgv_cart_strip' => array(
                'name' => __( 'Show Voucher Strip on Cart Page' ),
                'type' => 'checkbox',
                'desc' => __( 'Encourage users to add more items to the cart if minimum order value is not matched.', 'woo-gift-vouchers' ),
                'id'    => 'wc_wgv_cart_strip',
            ),
            'wgv_strip_location' => array(
                'name' => __( 'Where to show?' ),
                'type' => 'select',
                'id'    => 'wgv_strip_location',
                "default" => "woocommerce_before_cart_table",
                'options' => array(
                        "woocommerce_before_cart_table" => "Above Cart Table",
                        "woocommerce_proceed_to_checkout" => "Above Proceed to Checkout Button",
                        "woocommerce_after_cart_totals" => "Below Proceed to Checkout Button",
                    )
            ),
            'strip_text_color' => array(
                'name' => __( 'Strip Text Color', 'woo-gift-vouchers' ),
                'type' => 'color',
                'id'   => 'wgv_strip_text_color',
                'css'      => 'max-width: 80px;'
            ),
            'strip_success_bg_color' => array(
                'name' => __( 'Strip Success Background Color', 'woo-gift-vouchers' ),
                'type' => 'color',
                'id'   => 'wgv_strip_success_bg_color',
                'css'      => 'max-width: 80px;'
            ),
            'strip_warning_bg_color' => array(
                'name' => __( 'Strip Warning Background Color', 'woo-gift-vouchers' ),
                'type' => 'color',
                'id'   => 'wgv_strip_warning_bg_color',
                'css'      => 'max-width: 80px;'
            ),
            'strip_border_radius' => array(
                'name' => __( 'Strip Border Radius', 'woo-gift-vouchers' ),
                'type' => 'text',
                'placeholder' => __( 'Border Radius', 'woo-gift-vouchers' ),
                'default'   => "5px 5px 0px 0px",
                'desc' => __( 'You can also use CSS shorthands.', 'woo-gift-vouchers' ),
                'id'   => 'wc_' . $this->tab_id . '_strip_border_radius'
            ),
            'initial_strip_insider_text' => array(
                'name' => __( 'Text for insufficient Cart Value', 'woo-gift-vouchers' ),
                'type' => 'textarea',
                'desc' => __( "Text that will be shown if current cart has insuffecient value for claiming gift voucher.", "woo-gift-vouchers" ),
                'desc_tip' => true,
                'placeholder' => __( 'Text to show in Strip [Insufficient Cart Value]', 'woo-gift-vouchers' ),
                'default' => WGV_INSUFFICIENT_AMOUNT_STRIP_MSG,
                'id'   => 'wc_' . $this->tab_id . '_initial_strip_insider_text',
                'css'      => 'min-width: 50%; height: 75px;',
            ),
            'success_strip_insider_text' => array(
                'name' => __( 'Success Text to show in Strip', 'woo-gift-vouchers' ),
                'type' => 'textarea',
                'desc' => __( "Text that will be shown if user is eligible to get gift voucher.", "woo-gift-vouchers" ),
                'desc_tip' => true,
                'placeholder' => __( 'Text to show in Strip [Success]', 'woo-gift-vouchers' ),
                'default' => WGV_SUCCESS_STRIP_MSG,
                'id'   => 'wc_' . $this->tab_id . '_success_strip_insider_text',
                'css'      => 'min-width: 50%; height: 75px;',
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_' . $this->tab_id . '_section_end'
            )
		);

        if( ! isset( $_GET['section'] ) || '' === $_GET['section'] ){
		    return apply_filters( 'wc_woo-gift-vouchers_settings', $general_settings );
        }

        if( isset( $_GET['section'] ) || 'wgv-design-settings' === $_GET['section'] ){
            return apply_filters( 'wc_woo-gift-vouchers_settings', $design_settings );
        }
    }

    public function wgv_update_settings() {
        woocommerce_update_options( $this->wgv_settings_fields() );
    }

}

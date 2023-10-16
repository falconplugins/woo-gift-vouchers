<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://falconplugins.store
 * @since      1.0.0
 *
 * @package    Woo_Gift_Vouchers
 * @subpackage Woo_Gift_Vouchers/public/partials
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
class Woo_Gift_Vouchers_Admin_Actions {

    private $wgv_order_email_from;
    private $wgv_order_email_name;
    private $wgv_order_status;
    private $wc_wgv_email_subject;

	public function __construct() {

        $wgv_enable = get_option( 'wgv_enable' );

        if ( ! isset( $wgv_enable ) || 'yes' !== $wgv_enable ) {
            return;
        }

        // Change sender name
        $this->wgv_order_email_from     = get_option( 'wc_wgv_from_email' ) ? add_filter( 'woocommerce_email_from_name', function( $from_name, $wc_email ){ return get_option( 'wc_wgv_from_email' ); }, 10, 2 ) : get_option( 'woocommerce_email_from_address' );
        
        // Change email name
        $this->wgv_order_email_name     = get_option( 'wc_wgv_from_name' ) ? add_filter( 'woocommerce_email_from_address', function( $from_email, $wc_email ){ return get_option( 'wc_wgv_from_name' ); }, 10, 2 ) : get_option( 'woocommerce_email_from_name' );
        
        $this->wgv_order_status         = get_option( 'wc_wgv_email_status' ) ? get_option( 'wc_wgv_email_status' ) : "wc-completed";
        $this->wgv_email_subject        = get_option( 'wc_wgv_email_subject' ) ? get_option( 'wc_wgv_email_subject' ) : WGV_VOUCHER_EMAIL_SUBJECT;

        add_action('woocommerce_order_status_changed', array( $this, 'wc_wgv_status_change' ), 10, 3 );

	}

    public function wc_wgv_status_change( $order_id, $old_status, $new_status ) {

        if( "wc-" . $new_status === $this->wgv_order_status ){
            
            $order = wc_get_order($order_id);

            $wc_wgv_email_template = WGV_EMAIL_TEMPLATE;

            if( file_exists( get_stylesheet_directory() . "woo-gift-voucher/templates/email/woo-gift-voucher-email.php" ) ){
                $wc_wgv_email_template = get_stylesheet_directory() . "woo-gift-voucher/templates/email/woo-gift-voucher-email.php";
            }

            $order_data         = $order->get_data();
            $wgv_to_email       = $order_data['billing']['email'];
            $wgv_billing_name   = $order_data['billing']['first_name'];

            $wgv_coupon_code = $this->wc_wgv_create_coupon_code( $order_id, $wgv_to_email, $wgv_billing_name );

            $replace_tags_array = [
                "{order_id}"            => $order_id,
                "{order_first_name}"    => $wgv_billing_name,
            ];

            foreach( $replace_tags_array as $tag => $text ) {
                $this->wgv_email_subject = str_replace( $tag, $text, $this->wgv_email_subject );
            }

            $wc_wgv_email_voucher = WC_Email::send( 
                $wgv_to_email, 
                $this->wgv_email_subject, 
                file_get_contents( $wc_wgv_email_template ), 
                WC_Email::get_headers()
            );

        }

    }

    public function wc_wgv_create_coupon_code( $order_id, $to_email, $to_name ) {
        
    }

}
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
class Woo_Gift_Vouchers_Public_Display {

	public function __construct() {

        $wgv_strip_enable   = get_option( 'wc_wgv_cart_strip' ) ?? "no";

        if ( "yes" === $wgv_strip_enable ) {
            
            $wgv_strip_location = get_option( 'wgv_strip_location' ) ?? "woocommerce_before_cart_table";
            
            add_action( 
                $wgv_strip_location, 
                array(
                    $this,
                    'wgv_gift_voucher_strip'
                )
            );

        }

	}

    public function wgv_gift_voucher_strip() {
        
        $cart_value         = WC()->cart->subtotal;
        $wgv_min_order_val  = get_option( 'wc_wgv-settings_gift_amount' );
        $current_WC_symbol  = get_woocommerce_currency_symbol();

        $wgv_min_order_val_decimal = $current_WC_symbol . number_format( 
            $wgv_min_order_val, 
            2, 
            '.', 
            ' ' 
        );

        $strip_theme = "success";
        $strip_text = get_option( 'wc_wgv-settings_success_strip_insider_text' );
        $wgv_cart_rem_value = "";
        if( $cart_value < $wgv_min_order_val ){
            $strip_theme = "warning";
            $strip_text = get_option( 'wc_wgv-settings_initial_strip_insider_text' );
            $wgv_cart_rem_value = $current_WC_symbol . $wgv_min_order_val - $cart_value;
        }

        $str_replace_array = [
            "[wgv_number_of_vouchers]"  => floor($cart_value / $wgv_min_order_val ),
            "[wgv_rem_amount]"          => $wgv_cart_rem_value,
            "[wgv_gift_amount]"         => $wgv_min_order_val_decimal
        ];

        foreach( $str_replace_array as $tag => $text ){
            $strip_text = str_replace( $tag, $text, $strip_text );
        }

        ob_start();
        ?>
            <style scoped>

                .wc_wgv_success,
                .wc_wgv_warning {
                    border-radius: <?php echo get_option( "wc_wgv-settings_strip_border_radius" ) ?? "5px 5px 0px 0px"; ?>;
                    padding: 0.5rem 1em;
                }

                .wc_wgv_success {
                    background-color: <?php echo get_option( "wgv_strip_success_bg_color" ) ?? "#30f17e"; ?>;
                }

                .wc_wgv_warning {
                    background-color: <?php echo get_option( "wgv_strip_warning_bg_color" ) ?? "#30f17e"; ?>;
                }

                .wc_wgv_text {
                    color: <?php echo get_option( "wgv_strip_text_color" ) ?? "#000"; ?>;
                }

            </style>
            <div class="wc_wgv_<?php echo $strip_theme ?>">
                <p class="wc_wgv_text"><?php echo $strip_text; ?></p>
            </div>
        <?php

        echo ob_get_clean();

    }

}
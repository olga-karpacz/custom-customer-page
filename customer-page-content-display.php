<?php

/**
 * Plugin Name:      Customer page content display
 * Description:      This plugin allows to display additional columns on shop customer "My account" page - some are determined by                       product purchased by customer.
 * Author:           Olga Aleksandra Karpacz
 * Author URI:       https://studioafterglow.pl/
 */


//Adding and editing "My account" page endpoints

function cpcd_account_page_endpoints() {
    add_rewrite_endpoint( 'personalization', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'reports', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'contact', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'cpcd_account_page_endpoints' );

function cpcd_custom_query_vars( $vars ) {
    $vars[] = 'personalization';
    $vars[] = 'reports';
    $vars[] = 'contact';
    return $vars;
}
add_filter( 'query_vars', 'cpcd_custom_query_vars', 0 );

add_filter( 'woocommerce_account_menu_items', 'cpcd_menu_panel_nav' );

function cpcd_menu_panel_nav() {
    $items = array(
        'dashboard'         => __( 'Dashboard', 'woocommerce' ),
        'orders'            => __( 'Orders', 'woocommerce' ),
        'personalization'   => __( 'Personalization', 'woocommerce' ), 
        'contact'           => __( 'Contact', 'woocommerce' ), 
        'reports'           => __( 'Reports', 'woocommerce' ),
        'edit-address'      => __( 'Addresses', 'woocommerce' ),
        'customer-logout'   => __( 'Logout', 'woocommerce' ),
    );

    return $items;
}

//Setting content of "Personalization" tab, diffrent content based on ordered plan

function cpcd_personalization_content() {
    $customer_id = get_current_user_id(); 
    $last_order = wc_get_customer_last_order( $customer_id );
    if ( !empty( $last_order ) ) {
        $items = $last_order->get_items();
        $item = reset($items);
        $plan = $item->get_name();
        $variation = new WC_Product_Variation( $item[ 'variation_id' ] );
        $level = current( $variation->get_variation_attributes() );
        switch ($level) { 
            case "silver-plan":
                echo do_shortcode( '[contact-form-7 id="545" title="Personalization - silver"]' );
                break;
            case "golden-plan":
                echo do_shortcode( '[contact-form-7 id="692" title="Personalization - golden"]' );
                break;
            case "multiaccount":
                echo "<h1>User 1</h1>";
                echo do_shortcode( '[contact-form-7 id="682" title="Personalization"]' );
                echo "<h1 style='padding-top:30px'>User 2</h1>";
                echo do_shortcode( '[contact-form-7 id="682" title="Personalization"]' );
                break;
            default:
                echo do_shortcode( '[contact-form-7 id="682" title="Personalization"]' );
        }
?>
<script>
    (function($){
        $(".plan-field").val("<?php echo $plan; ?>"); //adding ordered plan as form field value
    })(jQuery);
</script>
<?php
    }
}

add_action( 'woocommerce_account_personalization_endpoint', 'cpcd_personalization_content' );

//Setting "Reports" tab content

function cpcd_reports_content() {
    $customer_id = get_current_user_id(); 
    $last_order = wc_get_customer_last_order( $customer_id );
    if ( !empty( $last_order ) ) {
        $items = $last_order->get_items();
        $item = reset( $items );
        $plan = $item->get_name();
        $variation = new WC_Product_Variation( $item['variation_id'] );
        $level = current( $variation->get_variation_attributes() );
        switch( $level ){
            case "multiaccount": //displaying 2 report forms for 2 users
                echo "<h1>User 1</h1>";
                echo do_shortcode( '[contact-form-7 id="555" title="Report"]' );
                echo "<h1 style='padding-top:30px'>User 2</h1>";
                echo do_shortcode( '[contact-form-7 id="555" title="Report"]' );
                break;
            default:
                echo do_shortcode( '[contact-form-7 id="555" title="Report"]' );
        }
?>
<script>
    (function($){
        $(".plan-field").val("<?php echo $plan; ?>"); //adding ordered plan as form field value
<?php
    }

}

add_action( 'woocommerce_account_reports_endpoint', 'cpcd_reports_content' );

//Setting "Contact" tab content

function cpcd_contact_content() {
    echo do_shortcode( '[contact-form-7 id="379" title="Contact"]' );
}

add_action( 'woocommerce_account_kontakt_endpoint', 'cpcd_contact_content' );

?>
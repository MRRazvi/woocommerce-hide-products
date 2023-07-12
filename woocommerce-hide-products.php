<?php

/*
 * Plugin Name:       WooCommerce Hide Products
 * Description:       Simple plugin to hide products on the basis of geolocation or popup country selection.
 * Version:           1.0.0
 * Author:            Zen Agency
 * Author URI:        https://zen.agency
 * Text Domain:       whp
 */

define('WHP_USER_COUNTRY', 'whp_user_country');

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(
        array(
            'page_title' => 'Akhurst Settings',
            'menu_title' => 'Akhurst Settings',
            'menu_slug' => 'akhurst-settings',
            'capability' => 'edit_posts',
            'redirect' => false
        )
    );
}

add_action('woocommerce_product_options_general_product_data', 'whp_add_hide_in_usa_checkbox');
function whp_add_hide_in_usa_checkbox()
{
    global $product_object;

    woocommerce_wp_checkbox(
        array(
            'id' => '_hide_in_usa',
            'label' => __('Hide In USA', 'text-domain'),
            'value' => get_post_meta($product_object->get_id(), '_hide_in_usa', true),
        )
    );
}

add_action('woocommerce_process_product_meta', 'whp_save_hide_in_usa_checkbox');
function whp_save_hide_in_usa_checkbox($product_id)
{
    $hide_in_usa = isset($_POST['_hide_in_usa']) ? 'yes' : 'no';
    update_post_meta($product_id, '_hide_in_usa', $hide_in_usa);
}

add_action('init', function () {
    if (is_admin()) {
        return;
    }


});
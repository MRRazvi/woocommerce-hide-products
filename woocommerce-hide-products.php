<?php

/*
 * Plugin Name:       WooCommerce Hide Products
 * Description:       Simple plugin to hide products on the basis of geolocation or popup country selection.
 * Version:           1.0.0
 * Author:            Zen Agency
 * Author URI:        https://zen.agency
 * Text Domain:       whp
 */

// init
define('WHP_USER_COUNTRY', 'whp_user_country');

// register options page
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

// skip if it is admin
if (is_admin()) {
    return;
}

// functionality according to option
$whp_functionality_option = get_field('whp_functionality_option', 'option');
$whp_user_country_page_url = get_field('whp_user_country_page_url', 'option');

if ($whp_functionality_option == '1') {
    whp_hide_products();
} else if ($whp_functionality_option == '2') {
    if (isset($_COOKIE[WHP_USER_COUNTRY])) {
        whp_hide_products();
    } else {
        if ($whp_user_country_page_url) {
            $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if ($actual_link != $whp_user_country_page_url) {
                echo '<script>window.location.href = "' . $whp_user_country_page_url . '";</script>';
            }
        }
    }
}

// [whp_user_country] shortcode
add_shortcode('whp_user_country', 'whp_user_country_func');
function whp_user_country_func($atts)
{
    ob_start();
    include_once __DIR__ . '/templates/shortcodes/whp_user_country.php';
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function whp_hide_products()
{
    var_dump('hide products');
}
<?php

/*
 * Plugin Name:       WooCommerce Hide Products
 * Description:       Simple plugin to hide products on the basis of geolocation or popup country selection.
 * Version:           1.0.0
 * Author:            Zen Agency
 * Author URI:        https://zen.agency
 * Text Domain:       whp
 */

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

$whp_functionality_option = get_field('whp_functionality_option', 'option');
if ($whp_functionality_option == '1') {

} else if ($whp_functionality_option == '2') {
    var_dump('22');
}
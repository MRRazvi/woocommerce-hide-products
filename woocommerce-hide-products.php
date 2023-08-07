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
define('WHP_USER_LOCATION', 'user_location');
define('WHP_POPUP_COOKIE', 'user_location_manual');
define('WHP_GFORM_ID', '1');

// optional acf options page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
}

// add custom field to product page
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

// store custom field from store page
add_action('woocommerce_process_product_meta', 'whp_save_hide_in_usa_checkbox');
function whp_save_hide_in_usa_checkbox($product_id)
{
    $hide_in_usa = isset($_POST['_hide_in_usa']) ? 'yes' : 'no';
    update_post_meta($product_id, '_hide_in_usa', $hide_in_usa);
}

// set cookie on form submission
add_action('gform_after_submission', 'whp_form_submission', 10, 2);
function whp_form_submission($entry, $form)
{
    if ($form['id'] == WHP_GFORM_ID) {
        $country = $entry['1'];
        setcookie(WHP_POPUP_COOKIE, $country, time() + (86400 * 30), '/');
    }
}

// exclude products from the loop if hide_in_usa is checked
// and user location is set to US
add_action('pre_get_posts', 'whp_exclude_products_in_usa');
function whp_exclude_products_in_usa($query)
{
    if (is_admin() || current_user_can('administrator')) {
        return;
    }

    if (!$query->is_main_query()) {
        return;
    }

    $location = ''; // default location

    $method = get_field('whp_functionality_option', 'option');

    if ($method == 1) {
        if (isset($_COOKIE[WHP_USER_LOCATION])) {
            $location = $_COOKIE[WHP_USER_LOCATION];
        }
    } else {
        if (isset($_COOKIE[WHP_POPUP_COOKIE])) {
            $location = $_COOKIE[WHP_POPUP_COOKIE];
        }
    }

    if ($location == 'US' || $location == 'United States') {
        $meta_query_args = array(
            'relation' => 'OR',
            array(
                'key' => '_hide_in_usa',
                'value' => 'yes',
                'compare' => '!='
            ),
            array(
                'key' => '_hide_in_usa',
                'compare' => 'NOT EXISTS'
            )
        );

        $query->set('meta_query', $meta_query_args);
    }
}

// utility to hide popup on method one
add_action('init', function () {
    $method = get_field('whp_functionality_option', 'option');
    if ($method == 1) {
        setcookie(WHP_POPUP_COOKIE, 'none', time() + (86400 * 30), '/');
    } else {
        if (isset($_COOKIE[WHP_POPUP_COOKIE]) && $_COOKIE[WHP_POPUP_COOKIE] == 'none') {
            setcookie(WHP_POPUP_COOKIE, '', time() - 3600, '/');
        }
    }
});

// helper method
if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '<pre>';
        die;
    }
}
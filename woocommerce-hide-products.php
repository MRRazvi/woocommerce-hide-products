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
    // if (is_admin()) {
    //     return;
    // }

});

function get_user_country_iso()
{
    $api_key = 'AIzaSyD1YRYXah31kLjisAR_8ydpSfCuh_JleHQ';
    $ip = $_SERVER['REMOTE_ADDR'];

    $url = "https://www.googleapis.com/geolocation/v1/geolocate?key=$api_key";
    $args = array(
        'method' => 'POST',
        'timeout' => 15,
        'sslverify' => false,
        'body' => json_encode(array('considerIp' => 'true')),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    );

    $response = wp_remote_post($url, $args);

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['location']['lat']) && isset($data['location']['lng'])) {
            $lat = $data['location']['lat'];
            $lng = $data['location']['lng'];

            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$api_key";
            $response = wp_remote_get($url);

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                if (isset($data['results'][0]['address_components'])) {
                    foreach ($data['results'][0]['address_components'] as $component) {
                        if (in_array('country', $component['types'])) {
                            return $component['short_name']; // ISO country code
                        }
                    }
                }
            }
        }
    }

    return false;
}

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '<pre>';
    die;
}
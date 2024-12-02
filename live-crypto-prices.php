<?php
/*
Plugin Name: Live Crypto Prices
Description: Displays live prices for Bitcoin, Ethereum, and Dogecoin.
Version: 1.0
Author: Manuf Rahman
*/

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Function to fetch cryptocurrency prices
function get_crypto_prices() {
    // API to fetch prices in both USD and INR
    $api_url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,dogecoin,ripple,cardano,solana,polkadot&vs_currencies=usd,inr&include_24hr_change=true';
    
    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) ) {
        return 'Error fetching data.';
    }

    $data = wp_remote_retrieve_body( $response );

    if ( empty( $data ) ) {
        return 'No data received.';
    }

    $prices = json_decode( $data, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return 'Error decoding data.';
    }

    // Output with both INR and USD prices
    $output = '<div class="crypto-prices">';
    $output .= '<h3>Live Cryptocurrency Prices</h3>';
    $output .= '<ul>';

    foreach ($prices as $crypto => $info) {
        $price_usd = $info['usd'];
        $price_inr = $info['inr'];
        $change = $info['usd_24h_change'];
        $indicator = $change >= 0 ? '↑' : '↓';
        $change_color = $change >= 0 ? 'up' : 'down';

        $output .= '<li>';
        $output .= '<span class="crypto-name">' . ucfirst($crypto) . '</span>';
        $output .= '<span class="crypto-price">$' . number_format($price_usd, 2) . ' | ₹' . number_format($price_inr, 2) . '</span>';
        $output .= '<span class="change ' . $change_color . '">' . $indicator . ' ' . number_format($change, 2) . '%</span>';
        $output .= '</li>';
    }

    $output .= '</ul>';
    $output .= '</div>';

    return $output;
}

// Shortcode to display the prices
function crypto_prices_shortcode() {
    return get_crypto_prices();
}

add_shortcode( 'crypto_prices', 'crypto_prices_shortcode' );

// Enqueue a stylesheet for styling (optional)
function crypto_prices_enqueue_styles() {
    wp_enqueue_style( 'crypto-prices-style', plugins_url( '/style.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'crypto_prices_enqueue_styles' );

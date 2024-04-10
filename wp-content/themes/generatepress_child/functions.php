<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */


/* 
* Custom Postal Codes Admin page 
*/

add_action( 'wp_enqueue_scripts', 'enqueue_validation_script' );

function enqueue_validation_script() {  
    
    if ( is_checkout() ) {

        wp_enqueue_script( 'custom-postal-code-validation', get_stylesheet_directory_uri().  '/postal-code-validation.js', array( 'jquery' ), '1.0', true );

    }

}

function tl_add_admin_page() {

    //Generate Admin Page
    add_menu_page( 'Postal Code Options', 'Postal Codes', 'manage_options', 'tl_postal_codes', 'tl_theme_create_page', 'dashicons-admin-multisite', 110 );

}
add_action('admin_menu', 'tl_add_admin_page');

function tl_theme_create_page() {     ?>

    <div class="wrap">
        <h2>Custom Postal codes</h2>
        <form method="post" action="options.php">
            <?php
                settings_fields('tl_postal_options_group');
                do_settings_sections('tl_custom_options');
                submit_button('Save Changes');
            ?>
        </form>
    </div>


<?php }

// Register Setting
function tl_custom_settings() {

    register_setting('tl_postal_options_group', 'tl_postal_codes', 'tl_sanitize_postal_codes');
    add_settings_section('tl_postal_codes_section', '', null, 'tl_custom_options');
    add_settings_field('tl_postal_codes_field', 'Postal codes', 'tl_postal_codes_field_html', 'tl_custom_options', 'tl_postal_codes_section');

}
add_action('admin_init', 'tl_custom_settings');

// Sanitize input field
function tl_sanitize_postal_codes($input) {
    $input = sanitize_text_field($input);
    return $input;
}

// Print HTML
function tl_postal_codes_field_html() {

    $postal_codes = get_option('tl_postal_codes'); 
    print_r($postal_codes);

    echo "<input type='text' name='tl_postal_codes' value='$postal_codes' />";
    echo "<p class='regular-text'>Enter Postal Codes (comma-separated)</p>";
    
}

// Check Shipping Postcode
function tl_check_shipping_postcode() {

    $customer = WC()->session->get('customer');    
    $postal_codes = explode( ', ', get_option('tl_postal_codes')); 

    // $postcode = isset($_POST['shipping_postcode']) ? sanitize_text_field($_POST['shipping_postcode']) : '';
    if( !is_cart() ) {
        if( !in_array($customer['shipping_postcode'], $postal_codes ) && !empty($customer['shipping_postcode']) ){
        
            wc_add_notice( __("Please enter a valid postcode. The list of zip codes: " . get_option('tl_postal_codes'), "woocommerce"), 'error' ); 
                       
        }

    }

}

add_action( 'woocommerce_check_cart_items', 'tl_check_shipping_postcode' );
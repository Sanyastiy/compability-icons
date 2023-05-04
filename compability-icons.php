<?php
/*
Plugin Name: Compability Icons for Gaming accessories
Description: This plugin allows to add 3 icons, PC, PS, XBOX on product page and manage them in Admin panel
Author: Alex Beontop
Version: 1.1
Author URI: https://github.com/Sanyastiy
License: GPLv3
Text Domain: compability-icons
*/


// Styles attachment
add_action('wp_enqueue_scripts', 'register_my_scripts');
function register_my_scripts()
{
    wp_enqueue_style('style', plugins_url('style.css', __FILE__));
}


// Adding attributes to the appearance on Admin Panel Page
function add_compability_icons_meta_box()
{
    add_meta_box(
        'compability_icons_meta_box',
        'Display Compability Icons',
        'compability_icons_meta_box_callback',
        'product',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_compability_icons_meta_box');


// Display Radio buttons in admin panel to manage Icons
function compability_icons_meta_box_callback($post)
{
    // Retreive data from DataBase
    $value_pc = get_post_meta($post->ID, 'display_pc', true);
    $value_ps = get_post_meta($post->ID, 'display_ps', true);
    $value_xbox = get_post_meta($post->ID, 'display_xbox', true);

    // Radiobuttons
?>
    <p>PC</p>
    <label><input type="radio" name="display_pc" value="non-compatible" <?php checked($value_pc, 'non-compatible'); ?>> Non-Compatible (hide icon)</label><br>
    <label><input type="radio" name="display_pc" value="compatible" <?php checked($value_pc, 'compatible'); ?>> Compatible</label><br>
    <p>PS</p>
    <label><input type="radio" name="display_ps" value="non-compatible" <?php checked($value_ps, 'non-compatible'); ?>> Non-Compatible (hide icon)</label><br>
    <label><input type="radio" name="display_ps" value="compatible" <?php checked($value_ps, 'compatible'); ?>> Compatible</label><br>
    <label><input type="radio" name="display_ps" value="ready" <?php checked($value_ps, 'ready'); ?>> Ready</label>
    <p>XBOX</p>
    <label><input type="radio" name="display_xbox" value="non-compatible" <?php checked($value_xbox, 'non-compatible'); ?>> Non-Compatible (hide icon)</label><br>
    <label><input type="radio" name="display_xbox" value="compatible" <?php checked($value_xbox, 'compatible'); ?>> Compatible</label><br>
    <label><input type="radio" name="display_xbox" value="ready" <?php checked($value_xbox, 'ready'); ?>> Ready</label>
<?php
}
// it is callback of already add_actioned function, so here no need to add add_action


// Save the Radio buttons values
function save_compability_icons_meta_box($post_id)
{
    if (isset($_POST['display_pc'])) {
        update_post_meta($post_id, 'display_pc', sanitize_text_field($_POST['display_pc']));
    }
    if (isset($_POST['display_ps'])) {
        update_post_meta($post_id, 'display_ps', sanitize_text_field($_POST['display_ps']));
    }
    if (isset($_POST['display_xbox'])) {
        update_post_meta($post_id, 'display_xbox', sanitize_text_field($_POST['display_xbox']));
    }
}
add_action('save_post_product', 'save_compability_icons_meta_box');


// Add Compability Icons custom field to all products
function add_compability_icons_custom_field_to_all_products() {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
    );

    $products = new WP_Query( $args );

    while ( $products->have_posts() ) {
        $products->the_post();

        // Get the post ID
        $post_id = get_the_ID();

        // Check if the meta fields already exist for this post ID
        $display_pc = get_post_meta( $post_id, 'display_pc', true );
        $display_ps = get_post_meta( $post_id, 'display_ps', true );
        $display_xbox = get_post_meta( $post_id, 'display_xbox', true );

        // ONLY If they don't exist, then add them, to prevent overgarbaging.
        if ( empty( $display_pc ) ) {
            add_post_meta( $post_id, 'display_pc', '' );
        } else {
            update_post_meta( $post_id, 'display_pc', $display_pc );
        }

        if ( empty( $display_ps ) ) {
            add_post_meta( $post_id, 'display_ps', '' );
        } else {
            update_post_meta( $post_id, 'display_ps', $display_ps );
        }

        if ( empty( $display_xbox ) ) {
            add_post_meta( $post_id, 'display_xbox', '' );
        } else {
            update_post_meta( $post_id, 'display_xbox', $display_xbox );
        }
    }

    wp_reset_query();
}



// Main display by conditions
function compability_icons_main_action()
{
    $display_pc = get_post_meta(get_the_ID(), 'display_pc', true);
    $display_ps = get_post_meta(get_the_ID(), 'display_ps', true);
    $display_xbox = get_post_meta(get_the_ID(), 'display_xbox', true);

    echo '<div class="product--compatibility-badges">';

    if ($display_pc == 'compatible') {
        echo '<div class="compatibility--badge is--pc compability-tooltip">PC
        <span class="tooltiptext">This product is compatible with PC.</span>
        </div>';
    }

    if ($display_ps == 'compatible') {
        echo '<div class="compatibility--badge is--ps compability-tooltip"><span>PS</span>
        <span class="tooltiptext">This product is compatible with PlayStation systems.</span>
        </div>';
    }

    if ($display_ps == 'ready') {
        echo '<div class="compatibility--badge is--ps-ready compability-tooltip"><span>PS <small>Ready</small></span>
        <span class="tooltiptext">This product becomes compatible with PlayStation systems when combined with a PlayStation-licensed Wheel Base.</span>
        </div>';
    }

    if ($display_xbox == 'compatible') {
        echo '<div class="compatibility--badge is--xbox compability-tooltip"><span>XBox</span>
        <span class="tooltiptext">This product is compatible with Xbox One and Xbox Series X|S.</span>
        </div>';
    }

    if ($display_xbox == 'ready') {
        echo '<div class="compatibility--badge is--xbox-ready compability-tooltip"><span>XBox <small>Ready</small></span>
        <span class="tooltiptext">This product becomes compatible with Xbox One and Xbox Series X|S systems when combined with an Xbox-licensed Steering Wheel.</span>
        </div>';
    }

    echo '</div>';
};
add_action('woocommerce_single_product_summary', 'compability_icons_main_action', 100);
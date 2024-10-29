<?php
/**
 * Plugin Name: Custom Image Sizes
 * Description: A simple plugin to add custom image sizes.
 * Version: 1.0
 * Author: Victor Malmis
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook to add the admin menu
add_action('admin_menu', 'cis_add_admin_menu');

function cis_add_admin_menu() {
    add_submenu_page('options-general.php', 'Custom Image Sizes', 'Custom Image Sizes', 'manage_options', 'custom-image-sizes', 'cis_options_page');
}

// Display the plugin settings page
function cis_options_page() {
    ?>
    <div class="wrap">
        <h1>Custom Image Sizes</h1>
        <form method="post" action="">
            <?php
            // Use nonce for security
            wp_nonce_field('cis_save_settings', 'cis_nonce');

            // Get the saved image sizes
            $image_sizes = get_option('cis_image_sizes', []);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Custom Image Size Name</th>
                    <td><input type="text" name="cis_image_size_name" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Width</th>
                    <td><input type="number" name="cis_image_size_width" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Height</th>
                    <td><input type="number" name="cis_image_size_height" required /></td>
                </tr>
            </table>
            <?php submit_button('Add Custom Image Size'); ?>
        </form>

        <?php
        // Display existing custom image sizes
        if ($image_sizes) {
            echo '<h2>Existing Custom Image Sizes</h2>';
            echo '<ul>';
            foreach ($image_sizes as $size) {
                echo '<li>' . esc_html($size['name']) . ' (' . esc_html($size['width']) . 'x' . esc_html($size['height']) . ')</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>
    <?php
}

// Handle form submission
add_action('admin_post_cis_save', 'cis_save_settings');

function cis_save_settings() {
    // Check nonce for security
    if (!isset($_POST['cis_nonce']) || !wp_verify_nonce($_POST['cis_nonce'], 'cis_save_settings')) {
        return;
    }

    // Validate input
    $name = sanitize_text_field($_POST['cis_image_size_name']);
    $width = intval($_POST['cis_image_size_width']);
    $height = intval($_POST['cis_image_size_height']);

    // Get current sizes
    $image_sizes = get_option('cis_image_sizes', []);

    // Add new size
    $image_sizes[] = [
        'name' => $name,
        'width' => $width,
        'height' => $height,
    ];

    // Update the option
    update_option('cis_image_sizes', $image_sizes);

    // Add the new image size
    add_image_size($name, $width, $height);

    // Redirect back to the settings page
    wp_redirect(admin_url('options-general.php?page=custom-image-sizes'));
    exit;
}
?>

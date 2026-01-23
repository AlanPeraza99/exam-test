<?php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'tailwind',
        'https://cdn.tailwindcss.com',
        [],
        null,
        false
    );
    wp_add_inline_script('tailwind', "
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#65B2E8',
              secondary: '#002D74',
              tertiary: '#98989A',
              whiteFont: '#ffffff'
            }
          }
        }
      }
    ");
    wp_enqueue_script(
        'theme-react-app',
        get_template_directory_uri() . '/assets/react/app.js',
        ['wp-element'],
        '1.0',
        true
    );
    wp_localize_script('theme-react-app', 'WP_DATA', [
        'restUrl' => esc_url_raw(rest_url()),
        'nonce'   => wp_create_nonce('wp_rest'),
    ]);
});
add_action('init', function () {
    register_post_type('testimonial', [
        'label'        => 'Testimonials',
        'public'       => true,
        'menu_icon'    => 'dashicons-format-quote',
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box('testimonial_meta', 'Client Info', function ($post) {
        $role = get_post_meta($post->ID, '_role', true);
        wp_nonce_field('save_testimonial_meta', 'testimonial_meta_nonce');
        echo '<input type="text" name="role" value="' . esc_attr($role) . '" style="width:100%;" />';
    }, 'testimonial');
});

add_action('save_post_testimonial', function ($post_id) {
    if (!isset($_POST['testimonial_meta_nonce']) || !wp_verify_nonce($_POST['testimonial_meta_nonce'], 'save_testimonial_meta')) return;
    update_post_meta($post_id, '_role', sanitize_text_field($_POST['role'] ?? ''));
});

add_action('after_setup_theme', function () {
    register_nav_menus([
        'primary' => 'Primary Menu',
    ]);

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
});

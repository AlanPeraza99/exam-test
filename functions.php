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

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
              whiteFont: '#ffff',
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

    // Data for JS
    wp_localize_script('theme-react-app', 'WP_DATA', [
        'restUrl' => esc_url_raw(rest_url()),
        'nonce'   => wp_create_nonce('wp_rest'),
    ]);
});

add_action('after_setup_theme', function () {
    register_nav_menus([
        'primary' => 'Primary Menu',
    ]);

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
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

add_action('init', function () {
    register_post_type('product', [
        'label'        => 'Products',
        'public'       => true,
        'menu_icon'    => 'dashicons-cart',
        'supports'     => ['title', 'thumbnail'],
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'products'],
        'show_in_rest' => true,
    ]);

    register_post_meta('product', 'price', [
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => '__return_true',
    ]);
});

add_action('init', function () {
    register_post_type('inquiry', [
        'labels' => [
            'name' => 'Inquiries',
            'singular_name' => 'Inquiry',
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-email-alt2',
        'supports' => ['title'],
    ]);
});

add_action('add_meta_boxes', function () {

    // Testimonials meta
    add_meta_box('testimonial_meta', 'Client Info', function ($post) {
        $role = get_post_meta($post->ID, '_role', true);
        wp_nonce_field('save_testimonial_meta', 'testimonial_meta_nonce');
        echo '<input type="text" name="role" value="' . esc_attr($role) . '" style="width:100%;" />';
    }, 'testimonial');

    // Product price meta
    add_meta_box('product_meta_price', 'Product Price', function ($post) {
        $price = get_post_meta($post->ID, 'price', true);
        wp_nonce_field('save_product_price', 'product_price_nonce');
        echo '<input type="text" name="product_price" value="' . esc_attr($price) . '" style="width:100%;" />';
    }, 'product');
});

add_action('save_post_testimonial', function ($post_id) {
    if (!isset($_POST['testimonial_meta_nonce']) || !wp_verify_nonce($_POST['testimonial_meta_nonce'], 'save_testimonial_meta')) return;
    update_post_meta($post_id, '_role', sanitize_text_field($_POST['role'] ?? ''));
});

add_action('save_post_product', function ($post_id) {
    if (!isset($_POST['product_price_nonce']) || !wp_verify_nonce($_POST['product_price_nonce'], 'save_product_price')) return;
    update_post_meta($post_id, 'price', sanitize_text_field($_POST['product_price'] ?? ''));
});

add_action('customize_register', function ($wp_customize) {

    $wp_customize->add_section('home_banner', [
        'title' => 'Home Banner',
        'priority' => 30,
    ]);

    $wp_customize->add_setting('home_banner_title', [
        'default' => 'Welcome to Velovita',
        'sanitize_callback' => 'sanitize_text_field',
    ]);


    $wp_customize->add_control('home_banner_title', [
        'label' => 'Banner Title',
        'section' => 'home_banner',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('home_banner_subtitle', [
        'default'           => 'Premium wellness products, crafted with care.',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('home_banner_subtitle', [
        'label'   => 'Banner Subtitle',
        'section' => 'home_banner',
        'type'    => 'text',
    ]);



    $wp_customize->add_setting('home_banner_bg', [
        'default'           => '',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Media_Control(
            $wp_customize,
            'home_banner_bg',
            [
                'label'    => 'Banner Background Image',
                'section'  => 'home_banner',
                'mime_type' => 'image',
            ]
        )
    );
});

add_action('rest_api_init', function () {
    register_rest_route('velovita/v1', '/inquiries', [
        'methods' => WP_REST_Server::CREATABLE,
        'permission_callback' => function ($request) {
            return wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest');
        },
        'callback' => function ($request) {

            $d = $request->get_json_params();

            $name = sanitize_text_field($d['name'] ?? '');
            $email = sanitize_email($d['email'] ?? '');
            $message = sanitize_textarea_field($d['message'] ?? '');
            $topic = sanitize_text_field($d['topic'] ?? '');

            if (!$name || !$email || !$message) {
                return new WP_REST_Response(['ok' => false], 422);
            }

            $id = wp_insert_post([
                'post_type' => 'inquiry',
                'post_status' => 'publish',
                'post_title' => "$name — $topic",
            ]);

            update_post_meta($id, 'name', $name);
            update_post_meta($id, 'email', $email);
            update_post_meta($id, 'topic', $topic);
            update_post_meta($id, 'message', $message);
            update_post_meta($id, 'consent', 'yes');

            wp_mail(get_option('admin_email'), 'New Inquiry', $message);
            wp_mail($email, 'We received your message', 'Thanks for contacting us.');

            return new WP_REST_Response(['ok' => true], 200);
        }
    ]);
});


add_filter('manage_inquiry_posts_columns', function ($cols) {
    unset($cols['date']);
    $cols['inq_name'] = 'Name';
    $cols['inq_email'] = 'Email';
    $cols['inq_topic'] = 'Topic';
    $cols['date'] = 'Date';
    return $cols;
});

add_action('manage_inquiry_posts_custom_column', function ($col, $id) {
    if ($col === 'inq_name') echo esc_html(get_post_meta($id, 'name', true));
    if ($col === 'inq_email') echo esc_html(get_post_meta($id, 'email', true));
    if ($col === 'inq_topic') echo esc_html(get_post_meta($id, 'topic', true));
}, 10, 2);

add_filter('get_custom_logo', function ($html) {
    return str_replace(
        '<img',
        '<img class="h-full w-auto max-w-full object-contain"',
        $html
    );
});

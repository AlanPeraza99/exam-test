<?php
$default_title    = 'Welcome to Velovita';
$default_subtitle = 'Premium wellness products, crafted with care.';
$default_cta_text = 'Shop now';
$default_cta_url  = home_url('/');

$title    = trim(get_theme_mod('home_banner_title'));
$subtitle = trim(get_theme_mod('home_banner_subtitle'));
$cta_text = trim(get_theme_mod('home_banner_cta_text'));
$cta_url  = trim(get_theme_mod('home_banner_cta_url'));
$bg_id    = absint(get_theme_mod('home_banner_bg'));

$title    = $title    ?: $default_title;
$subtitle = $subtitle ?: $default_subtitle;
$cta_text = $cta_text ?: $default_cta_text;
$cta_url  = $cta_url  ?: $default_cta_url;

$bg_url = $bg_id ? wp_get_attachment_image_url($bg_id, 'full') : '';
?>
<section class="relative overflow-hidden rounded-2xl shadow mb-12">
    <div class="absolute inset-0">
        <?php if ($bg_url): ?>
            <img
                src="<?php echo esc_url($bg_url); ?>"
                alt=""
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-secondary/70"></div>
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-secondary to-primary"></div>
        <?php endif; ?>
    </div>
    <div class="relative z-10 px-6 py-16 md:py-20 max-w-6xl mx-auto">
        <div class="max-w-2xl">
            <h1 class="text-3xl md:text-5xl font-bold text-whiteFont leading-tight">
                <?php echo esc_html($title); ?>
            </h1>

            <p class="mt-4 text-white/90 text-base md:text-lg">
                <?php echo esc_html($subtitle); ?>
            </p>

            <?php if (!empty($cta_text)): ?>
                <a
                    href="<?php echo esc_url($cta_url); ?>"
                    class="inline-flex mt-8 items-center justify-center px-6 py-3 rounded-lg bg-primary text-whiteFont font-semibold hover:opacity-90 transition">
                    <?php echo esc_html($cta_text); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
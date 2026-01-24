<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-primary h-16">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">

            <div class="font-bold tracking-tight text-whiteFont">
                <?php if (has_custom_logo()) : ?>
                    <div class="h-2 max-w-[140px] flex items-center">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    Logo
                <?php endif; ?>
            </div>

            <nav>
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'hidden md:flex items-center gap-6 text-sm font-medium text-whiteFont',
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

        </div>
    </header>

    <main>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<header class="border-b border-gray-200 bg-primary">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="text-font text-xl font-bold tracking-tight">
            Velovita
        </div>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-font text-whiteFont">
            <a href="#" class="hover:text-whiteFont">Home</a>
            <a href="#" class="hover:text-whiteFont">Products</a>
            <a href="#" class="hover:text-whiteFont">About</a>
            <a href="#" class="hover:text-whiteFont">Contact</a>
        </nav>
        <a
            href="#"
            class="hidden md:inline-flex items-center justify-center px-4 py-2 rounded-lg bg-secondary text-whiteFont text-sm font-medium hover:bg-gray-800">
            Contacto
        </a>
        <button class="md:hidden px-3 py-2 border rounded-lg text-sm">
            Menu
        </button>
    </div>
</header>

<main>

    <body <?php body_class(); ?>>
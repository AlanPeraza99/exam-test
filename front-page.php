<?php get_header(); ?>

<main class="p-10">

    <?php get_template_part('template-parts/banner'); ?>

    <?php get_template_part('template-parts/section-products'); ?>

    <div class="mt-10 text-center">
        <div id="react-promo-modal"></div>
    </div>
    <?php get_template_part('template-parts/testimonials'); ?>

    <div class="mt-10 text-center">
        <div id="react-inquiry-modal"></div>
    </div>


</main>

<?php get_footer(); ?>
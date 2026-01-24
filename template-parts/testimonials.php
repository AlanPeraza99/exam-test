<?php
$q = new WP_Query([
    'post_type'      => 'testimonial',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
]);

echo '<pre style="color:white">FOUND: ' . esc_html($q->found_posts) . '</pre>';
?>

<section class="py-20 bg-secondary text-white" id="testimonials">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-3xl font-bold mb-12 text-center">
            What Our Customers Say
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
            <?php
            $q = new WP_Query([
                'post_type' => 'testimonial',
                'posts_per_page' => 3,
            ]);

            if ($q->have_posts()):
                while ($q->have_posts()): $q->the_post();
                    $role = get_post_meta(get_the_ID(), '_role', true);
            ?>
                    <article class="bg-white text-gray-800 p-6 rounded-xl shadow">
                        <p class="italic mb-6">
                            “<?php echo wp_trim_words(get_the_content(), 30); ?>”
                        </p>

                        <div class="flex items-center gap-4">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('thumbnail', ['class' => 'w-12 h-12 rounded-full']); ?>
                            <?php endif; ?>

                            <div>
                                <p class="font-semibold"><?php the_title(); ?></p>
                                <?php if ($role): ?>
                                    <p class="text-sm text-gray-500"><?php echo esc_html($role); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>
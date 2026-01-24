<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-secondary">Products</h2>
                <p class="mt-2 text-tertiary">Explore our featured products.</p>
            </div>
            <a href="<?php echo esc_url(home_url('/')); ?>"
                class="text-secondary font-semibold hover:underline">
                View all
            </a>
        </div>

        <?php
        $q = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
        ]);
        ?>

        <?php if ($q->have_posts()): ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($q->have_posts()): $q->the_post(); ?>
                    <article class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow transition">
                        <a href="<?php the_permalink(); ?>" class="block">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('large', ['class' => 'w-full h-56 object-cover']); ?>
                            <?php else: ?>
                                <div class="w-full h-56 bg-gray-100"></div>
                            <?php endif; ?>
                        </a>

                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-secondary">
                                <a href="<?php the_permalink(); ?>" class="hover:underline">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <p class="mt-2 text-sm text-tertiary">
                                <?php echo esc_html(wp_trim_words(get_the_content(), 18)); ?>
                            </p>

                            <div class="mt-4">
                                <a href="<?php the_permalink(); ?>"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-primary text-white hover:opacity-90 transition">
                                    Learn more
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        <?php else: ?>
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-6 text-center">
                <p class="text-secondary font-semibold">No products found.</p>
                <p class="text-tertiary mt-1 text-sm">
                    Admin → Products → Add New → Publish.
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>
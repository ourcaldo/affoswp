<?php
/**
 * Search Results Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content">

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>
            <?php
            printf(
                /* translators: %s: search query */
                esc_html__('Hasil pencarian: "%s"', 'affos'),
                esc_html(get_search_query())
            );
            ?>
        </h1>
        <p>
            <?php
            printf(
                /* translators: %d: number of results */
                esc_html(_n('%d hasil ditemukan', '%d hasil ditemukan', $wp_query->found_posts, 'affos')),
                (int) $wp_query->found_posts
            );
            ?>
        </p>
    </div>
</section>

<!-- Search Results -->
<section class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="blog-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/card', 'blog', array('post' => get_post()));
                endwhile;
                ?>
            </div>

            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => '<span class="screen-reader-text">' . __('Sebelumnya', 'affos') . '</span><i class="ri-arrow-left-s-line" aria-hidden="true"></i>',
                'next_text' => '<span class="screen-reader-text">' . __('Selanjutnya', 'affos') . '</span><i class="ri-arrow-right-s-line" aria-hidden="true"></i>',
            ));
            ?>
        <?php else : ?>
            <div class="no-posts">
                <p><?php esc_html_e('Maaf, tidak ada hasil yang ditemukan. Silakan coba kata kunci lain.', 'affos'); ?></p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

</main>

<?php
get_footer();

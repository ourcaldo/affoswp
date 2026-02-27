<?php
/**
 * Main template file (fallback)
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="container">
        <?php if (have_posts()): ?>
            <div class="posts-grid">
                <?php while (have_posts()):
                    the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()): ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '<i class="ri-arrow-left-line"></i>',
                'next_text' => '<i class="ri-arrow-right-line"></i>',
            )); ?>

        <?php else: ?>
            <div class="no-posts">
                <h2><?php esc_html_e('Nothing Found', 'affos'); ?></h2>
                <p><?php esc_html_e('It seems we can\'t find what you\'re looking for.', 'affos'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();

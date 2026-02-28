<?php
/**
 * Template Part: Blog Card
 *
 * @package Affos
 * @since 1.0.0
 */

$post = isset($args['post']) ? $args['post'] : get_post();
$post_id = $post->ID;

// Get category
$categories = get_the_category($post_id);
$category_name = !empty($categories) ? $categories[0]->name : '';
$category_slug = !empty($categories) ? $categories[0]->slug : '';

// Badge class based on category
$badge_class = '';
if (in_array($category_slug, array('tips', 'tips-tricks'))) {
    $badge_class = 'tips';
} elseif ($category_slug === 'tutorial') {
    $badge_class = 'tutorial';
} elseif ($category_slug === 'opini') {
    $badge_class = 'opinion';
}

// Get author
$author_id = $post->post_author;
$author_name = get_the_author_meta('display_name', $author_id);

// Format date
$date = get_the_date('j M Y', $post_id);

// Reading time estimate
$content = $post->post_content;
$word_count = str_word_count(strip_tags($content));
$reading_time = max(1, ceil($word_count / 200));

// Is featured
$is_featured = isset($args['featured']) ? $args['featured'] : false;
?>

<article class="blog-card <?php echo $is_featured ? 'featured' : ''; ?>"
    data-category="<?php echo esc_attr($category_slug); ?>">
    <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="blog-link">
        <div class="blog-img">
            <?php if ($category_name): ?>
                <span class="blog-cat-badge <?php echo esc_attr($badge_class); ?>">
                    <?php echo esc_html($category_name); ?>
                </span>
            <?php endif; ?>
            <?php if (has_post_thumbnail($post_id)): ?>
                <?php echo get_the_post_thumbnail($post_id, 'medium_large', array('class' => 'blog-cover')); ?>
            <?php else: ?>
                <div class="blog-img-placeholder">
                    <i class="ri-article-line" aria-hidden="true"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="blog-content">
            <div class="blog-meta">
                <span class="blog-date"><i class="ri-calendar-line"></i>
                    <?php echo esc_html($date); ?>
                </span>
                <span class="blog-read"><i class="ri-time-line"></i>
                    <?php printf(esc_html__('%d min read', 'affos'), $reading_time); ?>
                </span>
            </div>
            <?php if ($is_featured): ?>
                <h2 class="blog-title">
                    <?php echo esc_html($post->post_title); ?>
                </h2>
                <?php if ($post->post_excerpt): ?>
                    <p class="blog-excerpt">
                        <?php echo esc_html(wp_trim_words($post->post_excerpt, 25)); ?>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <h3 class="blog-title">
                    <?php echo esc_html($post->post_title); ?>
                </h3>
            <?php endif; ?>
            <div class="blog-author">
                <div class="author-avatar">
                    <?php echo get_avatar($author_id, 32); ?>
                </div>
                <?php if ($is_featured): ?>
                    <div class="author-info">
                        <span class="author-name">
                            <?php echo esc_html($author_name); ?>
                        </span>
                        <span class="author-role">
                            <?php esc_html_e('Affos Editorial', 'affos'); ?>
                        </span>
                    </div>
                <?php else: ?>
                    <span class="author-name">
                        <?php echo esc_html($author_name); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</article>
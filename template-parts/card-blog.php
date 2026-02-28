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
    <div class="blog-card-img cat-blog">
        <?php if (has_post_thumbnail($post_id)): ?>
            <?php echo get_the_post_thumbnail($post_id, 'medium_large', array('class' => 'blog-cover')); ?>
        <?php else: ?>
            <i class="ri-article-line" aria-hidden="true"></i>
        <?php endif; ?>
    </div>
    <div class="blog-card-body">
        <?php if ($category_name): ?>
            <span class="category-label"><?php echo esc_html($category_name); ?></span>
        <?php endif; ?>
        <h3><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($post->post_title); ?></a></h3>
        <div class="blog-meta">
            <span><?php echo esc_html($author_name); ?></span>
            <span class="dot"></span>
            <span><?php echo esc_html($date); ?></span>
            <span class="dot"></span>
            <span><?php printf(esc_html__('%d menit baca', 'affos'), $reading_time); ?></span>
        </div>
    </div>
</article>

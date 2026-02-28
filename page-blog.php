<?php
/**
 * Blog Archive Template (page-blog.php)
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

// Get categories for filter
$categories = get_categories(array(
    'hide_empty' => false,
    'number' => 5,
));

$post_count = wp_count_posts('post')->publish;
?>

<main id="main-content">

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php esc_html_e('Blog', 'affos'); ?></h1>
        <p><?php esc_html_e('Berita teknologi, tips, dan panduan dari tim editorial Affos.', 'affos'); ?></p>
    </div>
</section>

<!-- Filter Bar -->
<div class="container">
    <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">
            <?php esc_html_e('Semua', 'affos'); ?>
        </button>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn" data-filter="<?php echo esc_attr($category->slug); ?>">
                    <?php echo esc_html($category->name); ?>
                </button>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="filter-search">
            <i class="ri-search-line" aria-hidden="true"></i>
            <input type="text" placeholder="<?php esc_attr_e('Cari artikel...', 'affos'); ?>" aria-label="<?php esc_attr_e('Cari artikel', 'affos'); ?>">
        </div>
    </div>
</div>

<?php
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$blog_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'paged'          => $paged,
    'posts_per_page' => get_option('posts_per_page'),
));

$first = true;
?>

<?php if ($blog_query->have_posts()): ?>
    <!-- Featured Post -->
    <section class="section">
        <div class="container">
            <?php
            $blog_query->the_post();
            get_template_part('template-parts/card', 'blog', array(
                'post' => get_post(),
                'featured' => true,
            ));
            ?>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="section">
        <div class="container">
            <div class="blog-grid">
                <?php while ($blog_query->have_posts()):
                    $blog_query->the_post();
                    get_template_part('template-parts/card', 'blog', array(
                        'post' => get_post(),
                    ));
                endwhile; ?>
            </div>

            <?php
            $big = 999999999;
            echo paginate_links(array(
                'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $blog_query->max_num_pages,
                'mid_size'  => 2,
                'prev_text' => '<span class="screen-reader-text">' . __('Sebelumnya', 'affos') . '</span><i class="ri-arrow-left-s-line" aria-hidden="true"></i>',
                'next_text' => '<span class="screen-reader-text">' . __('Selanjutnya', 'affos') . '</span><i class="ri-arrow-right-s-line" aria-hidden="true"></i>',
            ));
            wp_reset_postdata();
            ?>
        </div>
    </section>
<?php else: ?>
    <section class="section">
        <div class="container">
            <div class="no-posts">
                <p><?php esc_html_e('Belum ada artikel.', 'affos'); ?></p>
            </div>
        </div>
    </section>
<?php endif; ?>

</main>

<?php
get_footer();

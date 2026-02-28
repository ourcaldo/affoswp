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
?>

<!-- Hero Section - Blog (Amber Theme) -->
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-content">
            <div class="blog-hero-text">
                <span class="blog-badge">
                    <i class="ri-newspaper-line"></i>
                    <?php esc_html_e('Berita & Artikel', 'affos'); ?>
                </span>
                <h1>
                    <?php esc_html_e('Tech News', 'affos'); ?><br><span>
                        <?php esc_html_e('& Insights', 'affos'); ?>
                    </span>
                </h1>
                <p>
                    <?php esc_html_e('Dapatkan update terbaru seputar teknologi, tips & tricks, dan berita industri gadget langsung dari tim editorial kami.', 'affos'); ?>
                </p>
                <div class="blog-hero-stats">
                    <div class="stat-box">
                        <span class="stat-num">
                            <?php echo esc_html(wp_count_posts('post')->publish); ?>+
                        </span>
                        <span class="stat-label">
                            <?php esc_html_e('Artikel', 'affos'); ?>
                        </span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-num">10K+</span>
                        <span class="stat-label">
                            <?php esc_html_e('Pembaca', 'affos'); ?>
                        </span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-num">Daily</span>
                        <span class="stat-label">
                            <?php esc_html_e('Update', 'affos'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="blog-hero-visual">
                <div class="news-cards-stack">
                    <?php
                    // Get recent posts for visual
                    $recent_posts = get_posts(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                    ));

                    $card_class = 1;
                    foreach ($recent_posts as $post) {
                        $cats = get_the_category($post->ID);
                        $cat_name = !empty($cats) ? $cats[0]->name : __('Berita', 'affos');
                        ?>
                        <div class="news-card-mini card-<?php echo $card_class; ?>">
                            <div class="mini-cat">
                                <?php echo esc_html($cat_name); ?>
                            </div>
                            <div class="mini-headline">
                                <?php echo esc_html(wp_trim_words($post->post_title, 4, '...')); ?>
                            </div>
                        </div>
                        <?php
                        $card_class++;
                    }
                    wp_reset_postdata();

                    // Fallback
                    if (empty($recent_posts)) {
                        ?>
                        <div class="news-card-mini card-1">
                            <div class="mini-cat">Berita</div>
                            <div class="mini-headline">Apple Rilis iOS 18.3...</div>
                        </div>
                        <div class="news-card-mini card-2">
                            <div class="mini-cat">Tips</div>
                            <div class="mini-headline">10 Cara Hemat Baterai...</div>
                        </div>
                        <div class="news-card-mini card-3">
                            <div class="mini-cat">Tutorial</div>
                            <div class="mini-headline">Cara Setting NFC...</div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!-- Decorative elements -->
                <div class="blog-ring ring-1"></div>
                <div class="blog-ring ring-2"></div>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="blog-filter">
    <div class="container">
        <div class="filter-bar blog-filter-bar">
            <button class="filter-btn active" data-filter="all">
                <i class="ri-apps-line"></i>
                <?php esc_html_e('Semua', 'affos'); ?>
            </button>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category):
                    // Determine icon based on category
                    $icon = 'ri-newspaper-line';
                    $slug = $category->slug;
                    if (strpos($slug, 'tips') !== false) {
                        $icon = 'ri-lightbulb-line';
                    } elseif ($slug === 'tutorial') {
                        $icon = 'ri-book-open-line';
                    } elseif (strpos($slug, 'opini') !== false) {
                        $icon = 'ri-chat-quote-line';
                    }
                    ?>
                    <button class="filter-btn" data-filter="<?php echo esc_attr($slug); ?>">
                        <i class="<?php echo esc_attr($icon); ?>"></i>
                        <?php echo esc_html($category->name); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Blog Grid -->
<section class="blog-archive">
    <div class="container">
        <div class="blog-grid">
            <?php
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $blog_query = new WP_Query(array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'paged'          => $paged,
                'posts_per_page' => get_option('posts_per_page'),
            ));

            $first = true;
            if ($blog_query->have_posts()):
                while ($blog_query->have_posts()):
                    $blog_query->the_post();
                    get_template_part('template-parts/card', 'blog', array(
                        'post' => get_post(),
                        'featured' => $first,
                    ));
                    $first = false;
                endwhile;
            else:
                ?>
                <div class="no-posts">
                    <p>
                        <?php esc_html_e('Belum ada artikel.', 'affos'); ?>
                    </p>
                </div>
                <?php
            endif;
            ?>
        </div>

        <!-- Load More -->
        <div class="load-more">
            <?php
            $big = 999999999;
            echo paginate_links(array(
                'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $blog_query->max_num_pages,
                'mid_size'  => 2,
                'prev_text' => '<i class="ri-arrow-left-line"></i>',
                'next_text' => '<i class="ri-arrow-right-line"></i>',
            ));
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<?php
get_footer();

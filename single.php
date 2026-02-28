<?php
/**
 * Single Post Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

while (have_posts()) : the_post();
    $post_id = get_the_ID();

    // Get category
    $categories = get_the_category();
    $category_name = !empty($categories) ? $categories[0]->name : '';
    $category_slug = !empty($categories) ? $categories[0]->slug : '';

    // Get author info
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author_meta('display_name');
    $author_bio = get_the_author_meta('description');

    // Reading time
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = max(1, ceil($word_count / 200));
?>

<!-- Breadcrumb -->
<div class="container">
    <nav class="breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'affos'); ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Beranda', 'affos'); ?></a>
        <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
        <?php
        $blog_page = get_option('page_for_posts');
        $blog_url = $blog_page ? get_permalink($blog_page) : home_url('/blog/');
        ?>
        <a href="<?php echo esc_url($blog_url); ?>"><?php esc_html_e('Blog', 'affos'); ?></a>
        <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
        <span class="current-crumb"><?php echo esc_html(wp_trim_words(get_the_title(), 5)); ?></span>
    </nav>
</div>

<!-- Post Header -->
<div class="container">
    <div class="post-header">
        <?php if ($category_name) : ?>
            <span class="category-label"><?php echo esc_html($category_name); ?></span>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <p class="excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
        <div class="post-author-row">
            <div class="author-avatar">
                <?php echo get_avatar($author_id, 40); ?>
            </div>
            <span class="author-name"><?php echo esc_html($author_name); ?></span>
            <span>&middot;</span>
            <span><?php echo get_the_date('j M Y'); ?></span>
            <span>&middot;</span>
            <span><?php printf(__('%d menit baca', 'affos'), $reading_time); ?></span>
        </div>
    </div>
</div>

<!-- Post Hero Image -->
<div class="container">
    <div class="post-hero-img">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('full', array('class' => 'hero-image')); ?>
        <?php else: ?>
            <i class="ri-article-line" aria-hidden="true"></i>
        <?php endif; ?>
    </div>
</div>

<!-- Post Layout -->
<div class="container">
    <div class="post-layout">

        <!-- Article Content -->
        <article class="article-content">
            <?php the_content(); ?>

            <!-- Tags -->
            <?php
            $tags = get_the_tags();
            if ($tags) :
            ?>
            <div class="tag-list">
                <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-item"><?php echo esc_html($tag->name); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Author Box -->
            <div class="author-box">
                <div class="avatar-lg">
                    <?php echo get_avatar($author_id, 64); ?>
                </div>
                <div>
                    <h4><?php esc_html_e('Tentang Penulis', 'affos'); ?></h4>
                    <div class="author-name"><?php echo esc_html($author_name); ?></div>
                    <?php if ($author_bio) : ?>
                        <p><?php echo esc_html($author_bio); ?></p>
                    <?php else : ?>
                        <p><?php esc_html_e('Kontributor di Affos dengan fokus pada review gadget dan teknologi terkini.', 'affos'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Share Row -->
            <div class="share-row">
                <button class="share-btn" onclick="window.open('https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>', '_blank')">
                    <i class="ri-twitter-x-line" aria-hidden="true"></i> Twitter
                </button>
                <button class="share-btn" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>', '_blank')">
                    <i class="ri-facebook-line" aria-hidden="true"></i> Facebook
                </button>
                <button class="share-btn" onclick="window.open('https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>', '_blank')">
                    <i class="ri-whatsapp-line" aria-hidden="true"></i> WhatsApp
                </button>
                <button class="share-btn" onclick="navigator.clipboard.writeText('<?php echo esc_js(get_permalink()); ?>')">
                    <i class="ri-link" aria-hidden="true"></i> Copy Link
                </button>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- TOC Card -->
            <div class="sidebar-card">
                <h4><?php esc_html_e('Daftar Isi', 'affos'); ?></h4>
                <nav class="toc-list" id="toc-nav">
                    <!-- Generated by JavaScript -->
                </nav>
            </div>

            <!-- Share Card -->
            <div class="sidebar-card">
                <h4><?php esc_html_e('Bagikan', 'affos'); ?></h4>
                <div class="sidebar-share-list">
                    <button class="share-btn" onclick="window.open('https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>', '_blank')">
                        <i class="ri-twitter-x-line" aria-hidden="true"></i> Twitter
                    </button>
                    <button class="share-btn" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>', '_blank')">
                        <i class="ri-facebook-line" aria-hidden="true"></i> Facebook
                    </button>
                    <button class="share-btn" onclick="window.open('https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>', '_blank')">
                        <i class="ri-whatsapp-line" aria-hidden="true"></i> WhatsApp
                    </button>
                    <button class="share-btn" onclick="navigator.clipboard.writeText('<?php echo esc_js(get_permalink()); ?>')">
                        <i class="ri-link" aria-hidden="true"></i> Copy Link
                    </button>
                </div>
            </div>

            <!-- Newsletter Card -->
            <div class="sidebar-card">
                <h4><?php esc_html_e('Newsletter', 'affos'); ?></h4>
                <p class="sidebar-desc"><?php esc_html_e('Dapatkan tips gadget langsung di inbox Anda.', 'affos'); ?></p>
                <form action="#" method="post">
                    <input type="email" placeholder="<?php esc_attr_e('Email Anda', 'affos'); ?>" class="sidebar-input" required>
                    <button type="submit" class="btn-primary sidebar-btn-full"><?php esc_html_e('Langganan', 'affos'); ?></button>
                </form>
            </div>
        </aside>

    </div>
</div>

<!-- Related Posts -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Artikel Terkait', 'affos'); ?></h2>
            <a href="<?php echo esc_url($blog_url); ?>" class="see-all">
                <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>
        <div class="blog-grid">
            <?php
            $related = get_posts(array(
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'post__not_in'   => array($post_id),
                'category__in'   => wp_list_pluck($categories, 'term_id'),
            ));

            foreach ($related as $post) {
                get_template_part('template-parts/card', 'blog', array('post' => $post));
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<?php
endwhile;
get_footer();

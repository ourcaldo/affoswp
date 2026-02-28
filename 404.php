<?php
/**
 * 404 Error Page Template
 *
 * @package Affos
 * @since 1.0.0
 */

// Don't include header/footer for 404
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class('error-body'); ?>>
    <?php wp_body_open(); ?>

    <div class="error-page-full">
        <div class="error-content">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="error-logo">
                <?php echo esc_html(get_bloginfo('name')); ?><span>.</span>
            </a>

            <div class="error-visual">
                <div class="error-number">
                    <span class="num-4 num-left">4</span>
                    <div class="error-icon">
                        <i class="ri-smartphone-line" aria-hidden="true"></i>
                    </div>
                    <span class="num-4 num-right">4</span>
                </div>
            </div>

            <h1>
                <?php esc_html_e('Halaman Tidak Ditemukan', 'affos'); ?>
            </h1>
            <p>
                <?php esc_html_e('Maaf, halaman yang Anda cari tidak ditemukan atau sudah dipindahkan.', 'affos'); ?>
            </p>

            <div class="error-links">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <i class="ri-home-line" aria-hidden="true"></i>
                    <?php esc_html_e('Kembali ke Beranda', 'affos'); ?>
                </a>
            </div>

            <div class="quick-links">
                <span>
                    <?php esc_html_e('Atau coba halaman ini:', 'affos'); ?>
                </span>
                <div class="quick-link-grid">
                    <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="quick-link">
                        <i class="ri-smartphone-line" aria-hidden="true"></i>
                        <span>
                            <?php esc_html_e('Produk', 'affos'); ?>
                        </span>
                    </a>
                    <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>" class="quick-link">
                        <i class="ri-star-line" aria-hidden="true"></i>
                        <span>
                            <?php esc_html_e('Ulasan', 'affos'); ?>
                        </span>
                    </a>
                    <?php
                    $blog_page = get_option('page_for_posts');
                    $blog_url = $blog_page ? get_permalink($blog_page) : home_url('/blog/');
                    ?>
                    <a href="<?php echo esc_url($blog_url); ?>" class="quick-link">
                        <i class="ri-newspaper-line" aria-hidden="true"></i>
                        <span>
                            <?php esc_html_e('Blog', 'affos'); ?>
                        </span>
                    </a>
                    <?php
                    $compare_page = get_option('affos_compare_page');
                    $compare_url = $compare_page ? get_permalink($compare_page) : home_url('/bandingkan/');
                    ?>
                    <a href="<?php echo esc_url($compare_url); ?>" class="quick-link">
                        <i class="ri-scales-3-line" aria-hidden="true"></i>
                        <span>
                            <?php esc_html_e('Bandingkan', 'affos'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Decorative elements -->
        <div class="error-decor decor-1"></div>
        <div class="error-decor decor-2"></div>
        <div class="error-decor decor-3"></div>
    </div>

    <?php wp_footer(); ?>
</body>

</html>
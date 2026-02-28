<?php
/**
 * Header template
 *
 * @package Affos
 * @since 1.0.0
 */

// Get compare page URL
$compare_page_id = get_option('affos_compare_page');
$compare_url = $compare_page_id ? get_permalink($compare_page_id) : home_url('/bandingkan/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-compare-url="<?php echo esc_url($compare_url); ?>">
    <?php wp_body_open(); ?>

    <a class="skip-link" href="#main-content"><?php esc_html_e('Langsung ke konten', 'affos'); ?></a>

    <!-- Header -->
    <header>
        <div class="container">
            <?php
            if (has_custom_logo()) {
                the_custom_logo();
            } else {
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                    <?php echo esc_html(get_bloginfo('name')); ?><span>.</span>
                </a>
                <?php
            }
            ?>

            <nav class="nav-links" aria-label="<?php esc_attr_e('Menu utama', 'affos'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'items_wrap' => '%3$s',
                    'fallback_cb' => 'affos_fallback_menu',
                    'depth' => 1,
                ));
                ?>
            </nav>

            <div class="header-actions">
                <button class="icon-btn search-toggle" id="search-toggle" aria-label="<?php esc_attr_e('Search', 'affos'); ?>">
                    <i class="ri-search-line" aria-hidden="true"></i>
                </button>
                <?php
                // Subscribe button - links to newsletter or custom URL
                $subscribe_url = apply_filters('affos_subscribe_url', '#newsletter');
                ?>
                <a href="<?php echo esc_url($subscribe_url); ?>" class="btn btn-primary btn-sm">
                    <?php esc_html_e('Langganan', 'affos'); ?>
                </a>

                <!-- Mobile menu toggle -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="<?php esc_attr_e('Menu', 'affos'); ?>" aria-expanded="false" aria-controls="mobile-menu">
                    <i class="ri-menu-line" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu navigasi', 'affos'); ?>">
        <div class="mobile-menu-content">
            <button class="mobile-menu-close" id="close-mobile-menu" aria-label="<?php esc_attr_e('Close menu', 'affos'); ?>">
                <i class="ri-close-line" aria-hidden="true"></i>
            </button>
            <nav class="mobile-nav" aria-label="<?php esc_attr_e('Menu mobile', 'affos'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'mobile-nav-links',
                    'fallback_cb' => 'affos_fallback_menu',
                    'depth' => 2,
                ));
                ?>
            </nav>
        </div>
    </div>

    <?php
    /**
     * Fallback menu when no menu is assigned
     */
    function affos_fallback_menu()
    {
        ?>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>">
            <?php esc_html_e('Beranda', 'affos'); ?>
        </a>
        <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">
            <?php esc_html_e('Produk', 'affos'); ?>
        </a>
        <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>">
            <?php esc_html_e('Ulasan', 'affos'); ?>
        </a>
        <?php
        $compare_page = get_option('affos_compare_page');
        if ($compare_page) {
            ?>
            <a href="<?php echo esc_url(get_permalink($compare_page)); ?>">
                <?php esc_html_e('Bandingkan', 'affos'); ?>
            </a>
            <?php
        }
        ?>
        <a href="<?php echo esc_url(home_url('/blog/')); ?>">
            <?php esc_html_e('Blog', 'affos'); ?>
        </a>
        <?php
    }

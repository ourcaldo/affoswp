<?php
/**
 * Footer template
 *
 * @package Affos
 * @since 1.0.0
 */
?>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                    <?php echo esc_html(get_bloginfo('name')); ?><span>.</span>
                </a>
                <?php if (!empty(get_bloginfo('description'))): ?>
                    <p><?php echo esc_html(get_bloginfo('description')); ?></p>
                <?php else: ?>
                    <p><?php esc_html_e('Platform referensi gadget #1 di Indonesia. Membantu Anda memilih gadget terbaik dengan data akurat dan ulasan jujur.', 'affos'); ?>
                    </p>
                <?php endif; ?>

                <div class="footer-social">
                    <?php
                    $social_links = array(
                        'instagram' => get_option('affos_instagram_url'),
                        'youtube' => get_option('affos_youtube_url'),
                        'twitter-x' => get_option('affos_twitter_url'),
                    );

                    foreach ($social_links as $platform => $url) {
                        if (!empty($url)) {
                            printf(
                                '<a href="%s" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="ri-%s-line" aria-hidden="true"></i></a>',
                                esc_url($url),
                                esc_attr(ucfirst($platform)),
                                esc_attr($platform)
                            );
                        }
                    }

                    // Default social icons if none set
                    if (empty(array_filter($social_links))) {
                        ?>
                        <a href="#" class="social-btn" aria-label="Instagram"><i class="ri-instagram-line" aria-hidden="true"></i></a>
                        <a href="#" class="social-btn" aria-label="YouTube"><i class="ri-youtube-line" aria-hidden="true"></i></a>
                        <a href="#" class="social-btn" aria-label="Twitter"><i class="ri-twitter-x-line" aria-hidden="true"></i></a>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- Categories Column -->
            <div class="footer-col">
                <h4><?php esc_html_e('Kategori', 'affos'); ?></h4>
                <ul>
                    <?php
                    $product_categories = get_terms(array(
                        'taxonomy' => 'product_category',
                        'hide_empty' => false,
                        'number' => 4,
                    ));

                    if (!is_wp_error($product_categories) && !empty($product_categories)) {
                        foreach ($product_categories as $category) {
                            printf(
                                '<li><a href="%s">%s</a></li>',
                                esc_url(get_term_link($category)),
                                esc_html($category->name)
                            );
                        }
                    } else {
                        // Fallback links
                        ?>
                        <li><a
                                href="<?php echo esc_url(home_url('/produk/?category=smartphone')); ?>"><?php esc_html_e('Smartphone', 'affos'); ?></a>
                        </li>
                        <li><a
                                href="<?php echo esc_url(home_url('/produk/?category=laptop')); ?>"><?php esc_html_e('Laptop', 'affos'); ?></a>
                        </li>
                        <li><a
                                href="<?php echo esc_url(home_url('/produk/?category=tablet')); ?>"><?php esc_html_e('Tablet', 'affos'); ?></a>
                        </li>
                        <li><a
                                href="<?php echo esc_url(home_url('/produk/?category=aksesoris')); ?>"><?php esc_html_e('Aksesoris', 'affos'); ?></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <!-- Company Column -->
            <div class="footer-col">
                <h4><?php esc_html_e('Perusahaan', 'affos'); ?></h4>
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => '',
                        'depth'          => 1,
                    ));
                } else {
                    // Fallback when no menu is assigned
                    $about_page = get_option('affos_about_page');
                    $contact_page = get_option('affos_contact_page');
                    ?>
                    <ul>
                        <li><a
                                href="<?php echo $about_page ? esc_url(get_permalink($about_page)) : '#'; ?>"><?php esc_html_e('Tentang Kami', 'affos'); ?></a>
                        </li>
                        <li><a
                                href="<?php echo $contact_page ? esc_url(get_permalink($contact_page)) : '#'; ?>"><?php esc_html_e('Kontak', 'affos'); ?></a>
                        </li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <!-- Legal Column -->
            <div class="footer-col">
                <h4><?php esc_html_e('Legal', 'affos'); ?></h4>
                <ul>
                    <?php
                    $terms_page = get_option('affos_terms_page');
                    $privacy_page = get_option('wp_page_for_privacy_policy');
                    ?>
                    <li><a
                            href="<?php echo $terms_page ? esc_url(get_permalink($terms_page)) : '#'; ?>"><?php esc_html_e('Syarat & Ketentuan', 'affos'); ?></a>
                    </li>
                    <li><a
                            href="<?php echo $privacy_page ? esc_url(get_permalink($privacy_page)) : '#'; ?>"><?php esc_html_e('Kebijakan Privasi', 'affos'); ?></a>
                    </li>
                    <li><a href="#"><?php esc_html_e('Disclaimer', 'affos'); ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>.
                <?php esc_html_e('Semua hak dilindungi.', 'affos'); ?></span>
            <span><?php esc_html_e('Dibuat di Indonesia', 'affos'); ?></span>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
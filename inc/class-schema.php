<?php
/**
 * Structured Data (JSON-LD Schema.org Markup)
 *
 * Outputs JSON-LD structured data for:
 * - Product (single-product)
 * - Review (single-review)
 * - Article (single blog post)
 * - BreadcrumbList (all pages with breadcrumbs)
 * - WebSite (site-wide)
 *
 * @package Affos
 * @since 1.1.4
 */

if (!defined('ABSPATH')) {
    exit;
}

class Affos_Schema
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'output_schema'), 5);
    }

    /**
     * Output appropriate schema based on current page
     */
    public function output_schema()
    {
        if (is_singular('product')) {
            $this->product_schema();
            $this->breadcrumb_schema();
        } elseif (is_singular('review')) {
            $this->review_schema();
            $this->breadcrumb_schema();
        } elseif (is_singular('post')) {
            $this->article_schema();
            $this->breadcrumb_schema();
        } elseif (is_front_page()) {
            $this->website_schema();
        }
    }

    /**
     * Output a JSON-LD script tag
     */
    private function render($data)
    {
        echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }

    /**
     * Product schema for single-product pages
     */
    private function product_schema()
    {
        $post_id = get_the_ID();
        $price_raw = get_post_meta($post_id, '_misc_price', true);
        $brand_terms = get_the_terms($post_id, 'product_brand');
        $brand_name = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';
        $category_terms = get_the_terms($post_id, 'product_category');
        $category_name = ($category_terms && !is_wp_error($category_terms)) ? $category_terms[0]->name : '';

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => get_the_title(),
            'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, ''),
            'url' => get_permalink(),
        );

        // Image
        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url($post_id, 'large');
        }

        // Brand
        if ($brand_name) {
            $schema['brand'] = array(
                '@type' => 'Brand',
                'name' => $brand_name,
            );
        }

        // Category
        if ($category_name) {
            $schema['category'] = $category_name;
        }

        // Offers (price)
        if ($price_raw) {
            $numeric_price = preg_replace('/[^0-9]/', '', $price_raw);
            if ($numeric_price) {
                $schema['offers'] = array(
                    '@type' => 'Offer',
                    'price' => $numeric_price,
                    'priceCurrency' => 'IDR',
                    'availability' => 'https://schema.org/InStock',
                    'url' => get_permalink(),
                );
            }
        }

        // Additional properties
        $chipset = get_post_meta($post_id, '_platform_chipset', true);
        $os = get_post_meta($post_id, '_platform_os', true);
        $weight = get_post_meta($post_id, '_body_weight', true);
        $color = get_post_meta($post_id, '_misc_colors', true);

        $additional = array();
        if ($chipset) {
            $additional[] = array('@type' => 'PropertyValue', 'name' => 'Processor', 'value' => $chipset);
        }
        if ($os) {
            $additional[] = array('@type' => 'PropertyValue', 'name' => 'Operating System', 'value' => $os);
        }
        if ($weight) {
            $additional[] = array('@type' => 'PropertyValue', 'name' => 'Weight', 'value' => $weight);
        }
        if ($color) {
            $additional[] = array('@type' => 'PropertyValue', 'name' => 'Color', 'value' => $color);
        }
        if (!empty($additional)) {
            $schema['additionalProperty'] = $additional;
        }

        // Linked review aggregate rating
        $reviews = get_posts(array(
            'post_type' => 'review',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_review_product_id',
                    'value' => $post_id,
                    'compare' => '=',
                ),
            ),
        ));

        if (!empty($reviews)) {
            $scores = array();
            foreach ($reviews as $review) {
                $score = (float) get_post_meta($review->ID, '_review_score', true);
                if ($score > 0) {
                    $scores[] = $score;
                }
            }
            if (!empty($scores)) {
                $schema['aggregateRating'] = array(
                    '@type' => 'AggregateRating',
                    'ratingValue' => number_format(array_sum($scores) / count($scores), 1),
                    'bestRating' => '10',
                    'worstRating' => '1',
                    'ratingCount' => count($scores),
                );
            }
        }

        $this->render($schema);
    }

    /**
     * Review schema for single-review pages
     */
    private function review_schema()
    {
        $review_id = get_the_ID();
        $score = (float) get_post_meta($review_id, '_review_score', true);
        $verdict = get_post_meta($review_id, '_review_verdict', true);
        $product_id = get_post_meta($review_id, '_review_product_id', true);
        $pros = get_post_meta($review_id, '_review_pros', true);
        $cons = get_post_meta($review_id, '_review_cons', true);

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'name' => get_the_title(),
            'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, ''),
            'url' => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name'),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url('/'),
            ),
        );

        // Image
        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url($review_id, 'large');
        }

        // Review rating
        if ($score > 0) {
            $schema['reviewRating'] = array(
                '@type' => 'Rating',
                'ratingValue' => number_format($score, 1),
                'bestRating' => '10',
                'worstRating' => '1',
            );
        }

        // Review body
        if ($verdict) {
            $schema['reviewBody'] = $verdict;
        }

        // Pros and cons
        if ($pros) {
            $pros_list = array_filter(array_map('trim', explode("\n", $pros)));
            if (!empty($pros_list)) {
                $schema['positiveNotes'] = array(
                    '@type' => 'ItemList',
                    'itemListElement' => array_map(function ($pro) {
                        return array('@type' => 'ListItem', 'name' => $pro);
                    }, array_values($pros_list)),
                );
            }
        }
        if ($cons) {
            $cons_list = array_filter(array_map('trim', explode("\n", $cons)));
            if (!empty($cons_list)) {
                $schema['negativeNotes'] = array(
                    '@type' => 'ItemList',
                    'itemListElement' => array_map(function ($con) {
                        return array('@type' => 'ListItem', 'name' => $con);
                    }, array_values($cons_list)),
                );
            }
        }

        // Item reviewed (linked product)
        if ($product_id) {
            $product = get_post($product_id);
            if ($product) {
                $item_reviewed = array(
                    '@type' => 'Product',
                    'name' => $product->post_title,
                    'url' => get_permalink($product_id),
                );
                if (has_post_thumbnail($product_id)) {
                    $item_reviewed['image'] = get_the_post_thumbnail_url($product_id, 'large');
                }
                $brand_terms = get_the_terms($product_id, 'product_brand');
                if ($brand_terms && !is_wp_error($brand_terms)) {
                    $item_reviewed['brand'] = array(
                        '@type' => 'Brand',
                        'name' => $brand_terms[0]->name,
                    );
                }
                $schema['itemReviewed'] = $item_reviewed;
            }
        }

        $this->render($schema);
    }

    /**
     * Article schema for single blog posts
     */
    private function article_schema()
    {
        $post_id = get_the_ID();

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, ''),
            'url' => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name'),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url('/'),
            ),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink(),
            ),
        );

        // Image
        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url($post_id, 'large');
        }

        // Word count
        $content = get_the_content();
        $word_count = str_word_count(strip_tags(strip_shortcodes($content)));
        if ($word_count > 0) {
            $schema['wordCount'] = $word_count;
        }

        $this->render($schema);
    }

    /**
     * BreadcrumbList schema
     */
    private function breadcrumb_schema()
    {
        $items = array();
        $position = 1;

        // Home
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Beranda', 'affos'),
            'item' => home_url('/'),
        );

        if (is_singular('product')) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => __('Produk', 'affos'),
                'item' => get_post_type_archive_link('product'),
            );

            $categories = get_the_terms(get_the_ID(), 'product_category');
            if ($categories && !is_wp_error($categories)) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $categories[0]->name,
                    'item' => get_term_link($categories[0]),
                );
            }
        } elseif (is_singular('review')) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => __('Ulasan', 'affos'),
                'item' => get_post_type_archive_link('review'),
            );
        } elseif (is_singular('post')) {
            $blog_page = get_option('page_for_posts');
            $blog_url = $blog_page ? get_permalink($blog_page) : home_url('/blog/');
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => __('Blog', 'affos'),
                'item' => $blog_url,
            );
        }

        // Current page (no item URL for last breadcrumb per Google specs)
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
        );

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        );

        $this->render($schema);
    }

    /**
     * WebSite schema for the homepage
     */
    private function website_schema()
    {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url('/'),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => array(
                    '@type' => 'EntryPoint',
                    'urlTemplate' => home_url('/?s={search_term_string}'),
                ),
                'query-input' => 'required name=search_term_string',
            ),
        );

        $this->render($schema);
    }
}

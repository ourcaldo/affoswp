<?php
/**
 * Open Graph & Twitter Card Meta Tags
 *
 * Outputs OG and Twitter meta tags for better social sharing.
 *
 * @package Affos
 * @since 1.1.4
 */

if (!defined('ABSPATH')) {
    exit;
}

class Affos_Social_Meta
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'output_meta'), 4);
    }

    /**
     * Output meta tags based on current page
     */
    public function output_meta()
    {
        $meta = $this->get_meta();
        if (empty($meta)) {
            return;
        }

        echo "\n<!-- Open Graph / Twitter Card -->\n";

        // Open Graph
        $this->tag('og:type', $meta['type']);
        $this->tag('og:title', $meta['title']);
        $this->tag('og:description', $meta['description']);
        $this->tag('og:url', $meta['url']);
        $this->tag('og:site_name', get_bloginfo('name'));
        $this->tag('og:locale', 'id_ID');

        if (!empty($meta['image'])) {
            $this->tag('og:image', $meta['image']);
            $this->tag('og:image:width', '1200');
            $this->tag('og:image:height', '630');
        }

        if (!empty($meta['published'])) {
            $this->tag('article:published_time', $meta['published']);
            $this->tag('article:modified_time', $meta['modified']);
        }

        // Twitter Card
        $this->tag('twitter:card', !empty($meta['image']) ? 'summary_large_image' : 'summary', 'name');
        $this->tag('twitter:title', $meta['title'], 'name');
        $this->tag('twitter:description', $meta['description'], 'name');

        if (!empty($meta['image'])) {
            $this->tag('twitter:image', $meta['image'], 'name');
        }

        echo "<!-- / Open Graph / Twitter Card -->\n";
    }

    /**
     * Get meta data for the current page
     */
    private function get_meta()
    {
        if (is_singular('product')) {
            return $this->product_meta();
        } elseif (is_singular('review')) {
            return $this->review_meta();
        } elseif (is_singular('post')) {
            return $this->article_meta();
        } elseif (is_singular('page')) {
            return $this->page_meta();
        } elseif (is_front_page()) {
            return $this->home_meta();
        } elseif (is_post_type_archive()) {
            return $this->archive_meta();
        } elseif (is_tax() || is_category() || is_tag()) {
            return $this->taxonomy_meta();
        }

        return array();
    }

    /**
     * Output a single meta tag
     */
    private function tag($property, $content, $attr = 'property')
    {
        if (empty($content)) {
            return;
        }
        printf(
            '<meta %s="%s" content="%s" />' . "\n",
            esc_attr($attr),
            esc_attr($property),
            esc_attr($content)
        );
    }

    /**
     * Get description from content
     */
    private function get_description($post_id = null)
    {
        if ($post_id) {
            $post = get_post($post_id);
            if ($post && $post->post_excerpt) {
                return wp_trim_words($post->post_excerpt, 30, '');
            }
            if ($post) {
                return wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 30, '');
            }
        }

        if (has_excerpt()) {
            return wp_trim_words(get_the_excerpt(), 30, '');
        }

        return wp_trim_words(wp_strip_all_tags(strip_shortcodes(get_the_content())), 30, '');
    }

    /**
     * Get featured image URL
     */
    private function get_image($post_id = null)
    {
        $id = $post_id ?: get_the_ID();
        if (has_post_thumbnail($id)) {
            return get_the_post_thumbnail_url($id, 'large');
        }
        return '';
    }

    /**
     * Product meta
     */
    private function product_meta()
    {
        $post_id = get_the_ID();
        $brand_terms = get_the_terms($post_id, 'product_brand');
        $brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';
        $price = get_post_meta($post_id, '_misc_price', true);

        $title = get_the_title();
        if ($brand) {
            $title .= ' - ' . $brand;
        }
        if ($price) {
            $title .= ' | ' . $price;
        }

        return array(
            'type' => 'product',
            'title' => $title,
            'description' => $this->get_description(),
            'url' => get_permalink(),
            'image' => $this->get_image(),
            'published' => '',
            'modified' => '',
        );
    }

    /**
     * Review meta
     */
    private function review_meta()
    {
        $review_id = get_the_ID();
        $score = get_post_meta($review_id, '_review_score', true);

        $title = get_the_title();
        if ($score) {
            $title .= ' - ' . number_format((float) $score, 1) . '/10';
        }

        return array(
            'type' => 'article',
            'title' => $title,
            'description' => $this->get_description(),
            'url' => get_permalink(),
            'image' => $this->get_image(),
            'published' => get_the_date('c'),
            'modified' => get_the_modified_date('c'),
        );
    }

    /**
     * Article/blog post meta
     */
    private function article_meta()
    {
        return array(
            'type' => 'article',
            'title' => get_the_title(),
            'description' => $this->get_description(),
            'url' => get_permalink(),
            'image' => $this->get_image(),
            'published' => get_the_date('c'),
            'modified' => get_the_modified_date('c'),
        );
    }

    /**
     * Page meta
     */
    private function page_meta()
    {
        return array(
            'type' => 'website',
            'title' => get_the_title(),
            'description' => $this->get_description(),
            'url' => get_permalink(),
            'image' => $this->get_image(),
            'published' => '',
            'modified' => '',
        );
    }

    /**
     * Homepage meta
     */
    private function home_meta()
    {
        return array(
            'type' => 'website',
            'title' => get_bloginfo('name') . ' - ' . get_bloginfo('description'),
            'description' => get_bloginfo('description'),
            'url' => home_url('/'),
            'image' => '',
            'published' => '',
            'modified' => '',
        );
    }

    /**
     * Archive meta
     */
    private function archive_meta()
    {
        $post_type = get_queried_object();
        $title = '';
        $description = '';

        if ($post_type && isset($post_type->label)) {
            $title = $post_type->label . ' - ' . get_bloginfo('name');
            $description = $post_type->description ?: $title;
        }

        return array(
            'type' => 'website',
            'title' => $title,
            'description' => $description,
            'url' => get_post_type_archive_link(get_post_type()),
            'image' => '',
            'published' => '',
            'modified' => '',
        );
    }

    /**
     * Taxonomy archive meta
     */
    private function taxonomy_meta()
    {
        $term = get_queried_object();
        if (!$term || !isset($term->name)) {
            return array();
        }

        return array(
            'type' => 'website',
            'title' => $term->name . ' - ' . get_bloginfo('name'),
            'description' => $term->description ?: $term->name,
            'url' => get_term_link($term),
            'image' => '',
            'published' => '',
            'modified' => '',
        );
    }
}

<?php
/**
 * Comments Template
 *
 * @package Affos
 * @since 1.1.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Do not display if password required and not entered
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            printf(
                esc_html(_n('%d Komentar', '%d Komentar', $comment_count, 'affos')),
                $comment_count
            );
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation(array(
            'prev_text' => '<i class="ri-arrow-left-s-line" aria-hidden="true"></i> ' . esc_html__('Komentar Sebelumnya', 'affos'),
            'next_text' => esc_html__('Komentar Berikutnya', 'affos') . ' <i class="ri-arrow-right-s-line" aria-hidden="true"></i>',
        ));
        ?>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php esc_html_e('Komentar ditutup.', 'affos'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'         => esc_html__('Tinggalkan Komentar', 'affos'),
        'title_reply_to'      => esc_html__('Balas ke %s', 'affos'),
        'cancel_reply_link'   => esc_html__('Batal', 'affos'),
        'label_submit'        => esc_html__('Kirim Komentar', 'affos'),
        'comment_notes_after' => '',
    ));
    ?>
</div>

/**
 * Affos Admin Settings Scripts
 *
 * Handles tab navigation, media uploader, color picker, and auto-save.
 *
 * @package Affos
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    // Save timeout for debouncing
    var saveTimeout = null;

    $(document).ready(function () {

        // Initialize color pickers
        initColorPickers();

        // Initialize tab navigation
        initTabNavigation();

        // Initialize media uploaders
        initMediaUploaders();

        // Initialize collapsible cards
        initCollapsibleCards();

        // Initialize char counters
        initCharCounters();

        // Initialize auto-save
        initAutoSave();
    });

    /**
     * Initialize color pickers
     */
    function initColorPickers() {
        if ($.fn.wpColorPicker) {
            $('.affos-color-picker').wpColorPicker({
                change: function () {
                    triggerAutoSave();
                }
            });
        }
    }

    /**
     * Initialize tab navigation
     */
    function initTabNavigation() {
        $('.affos-nav-item').on('click', function (e) {
            e.preventDefault();

            var tabId = $(this).data('tab');

            // Update nav items
            $('.affos-nav-item').removeClass('active');
            $(this).addClass('active');

            // Update tabs
            $('.affos-tab').removeClass('active');
            $('#tab-' + tabId).addClass('active');
        });
    }

    /**
     * Initialize media uploaders
     */
    function initMediaUploaders() {
        // Upload button
        $('.affos-upload-btn').on('click', function (e) {
            e.preventDefault();

            var button = $(this);
            var targetId = button.data('target');
            var preview = $('#' + targetId.replace('_id', '-preview').replace('_', '-'));

            // Create media frame
            var frame = wp.media({
                title: 'Select Image',
                button: { text: 'Use Image' },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();

                // Update hidden input
                $('#' + targetId).val(attachment.id);

                // Update preview
                var imgSize = targetId === 'favicon_id' ? 'thumbnail' : 'medium';
                var imgUrl = attachment.sizes[imgSize] ? attachment.sizes[imgSize].url : attachment.url;
                preview.html('<img src="' + imgUrl + '" alt="">');

                // Show remove button, hide upload button
                button.hide();
                button.siblings('.affos-remove-btn').show();

                // Trigger save
                triggerAutoSave();
            });

            frame.open();
        });

        // Remove button
        $('.affos-remove-btn').on('click', function (e) {
            e.preventDefault();

            var button = $(this);
            var targetId = button.data('target');
            var preview = $('#' + targetId.replace('_id', '-preview').replace('_', '-'));

            // Clear hidden input
            $('#' + targetId).val('0');

            // Clear preview
            preview.html('');

            // Show upload button, hide remove button
            button.hide();
            button.siblings('.affos-upload-btn').show();

            // Trigger save
            triggerAutoSave();
        });
    }

    /**
     * Initialize collapsible cards
     */
    function initCollapsibleCards() {
        $('.affos-card-toggle').on('click', function () {
            var card = $(this).closest('.affos-card-collapsible');
            card.toggleClass('open');
        });

        // Open first card by default
        $('.affos-card-collapsible').first().addClass('open');
    }

    /**
     * Initialize character counters
     */
    function initCharCounters() {
        $('input[maxlength], textarea[maxlength]').each(function () {
            updateCharCount($(this));
        }).on('input', function () {
            updateCharCount($(this));
        });
    }

    function updateCharCount(input) {
        var max = input.attr('maxlength');
        var current = input.val().length;
        var counter = input.siblings('.affos-char-count');

        if (counter.length === 0) {
            counter = input.parent().find('.affos-char-count');
        }

        counter.text(current + '/' + max);

        // Warning color when near limit
        if (current > max * 0.9) {
            counter.css('color', '#dc2626');
        } else if (current > max * 0.7) {
            counter.css('color', '#d97706');
        } else {
            counter.css('color', '');
        }
    }

    /**
     * Initialize auto-save
     */
    function initAutoSave() {
        // Watch for changes on form inputs
        $('#affos-settings-form').on('change', 'input, select, textarea', function () {
            triggerAutoSave();
        });

        // Also watch for text input with debounce
        $('#affos-settings-form').on('input', 'input[type="text"], input[type="number"], textarea', function () {
            triggerAutoSave();
        });
    }

    /**
     * Trigger auto-save with debounce
     */
    function triggerAutoSave() {
        // Clear existing timeout
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        // Show saving status
        $('.affos-save-status').removeClass('saved error').addClass('saving').text('Saving...');

        // Debounce save
        saveTimeout = setTimeout(function () {
            saveSettings();
        }, 800);
    }

    /**
     * Save settings via AJAX
     */
    function saveSettings() {
        var formData = $('#affos-settings-form').serialize();

        $.ajax({
            url: affosAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'affos_save_settings',
                nonce: affosAdmin.nonce,
                ...parseFormData(formData)
            },
            success: function (response) {
                if (response.success) {
                    $('.affos-save-status').removeClass('saving error').addClass('saved').text('✓ Saved');

                    // Clear status after 2 seconds
                    setTimeout(function () {
                        $('.affos-save-status').text('');
                    }, 2000);
                } else {
                    $('.affos-save-status').removeClass('saving saved').addClass('error').text('Error saving');
                }
            },
            error: function () {
                $('.affos-save-status').removeClass('saving saved').addClass('error').text('Error saving');
            }
        });
    }

    /**
     * Parse serialized form data into object
     */
    function parseFormData(formData) {
        var data = {};
        var pairs = formData.split('&');

        for (var i = 0; i < pairs.length; i++) {
            var pair = pairs[i].split('=');
            var key = decodeURIComponent(pair[0]);
            var value = decodeURIComponent(pair[1] || '');
            data[key] = value;
        }

        return data;
    }

})(jQuery);

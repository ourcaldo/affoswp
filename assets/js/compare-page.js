/**
 * Compare Page JavaScript
 *
 * Handles localStorage-based redirects and product removal
 * on the /bandingkan/ compare page.
 *
 * @package Affos
 * @since 1.1.4
 */

document.addEventListener('DOMContentLoaded', function () {
    if (!affosCompare.hasUrlProducts) {
        // Check localStorage for products and redirect if found
        var compareState = [];
        try { compareState = JSON.parse(localStorage.getItem('affos_compare') || '[]'); } catch(e) {}
        if (compareState.length >= 2) {
            // Redirect to SEO-friendly URL
            fetch(affosCompare.ajaxUrl + '?action=affos_get_compare_slugs&_ajax_nonce=' + affosCompare.nonce + '&ids=' + compareState.join(','))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success && data.data.url) {
                        window.location.href = data.data.url;
                    }
                })
                .catch(function(err) { console.error('Compare redirect failed:', err); });
        }
    } else {
        // Handle remove product button
        document.querySelectorAll('.remove-product-btn').forEach(function(btn) {
            btn.addEventListener('click', function () {
                var removeId = this.dataset.removeId;
                var compareState = [];
                try { compareState = JSON.parse(localStorage.getItem('affos_compare') || '[]'); } catch(e) {}
                compareState = compareState.filter(function(id) { return id !== removeId; });
                try { localStorage.setItem('affos_compare', JSON.stringify(compareState)); } catch(e) {}

                if (compareState.length < 2) {
                    window.location.href = affosCompare.compareUrl;
                } else {
                    // Redirect to new comparison URL
                    fetch(affosCompare.ajaxUrl + '?action=affos_get_compare_slugs&_ajax_nonce=' + affosCompare.nonce + '&ids=' + compareState.join(','))
                        .then(function(response) { return response.json(); })
                        .then(function(data) {
                            if (data.success && data.data.url) {
                                window.location.href = data.data.url;
                            }
                        })
                        .catch(function(err) { console.error('Compare redirect failed:', err); });
                }
            });
        });

        // Handle clear all button
        document.getElementById('clear-compare-all')?.addEventListener('click', function (e) {
            e.preventDefault();
            try { localStorage.setItem('affos_compare', '[]'); } catch(e) {}
            window.location.href = affosCompare.compareUrl;
        });
    }
});

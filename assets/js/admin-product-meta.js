jQuery(document).ready(function ($) {
    // Tab switching for specs meta box
    $('.affos-tab-btn').on('click', function () {
        var tab = $(this).data('tab');
        $('.affos-tab-btn').removeClass('active');
        $('.affos-tab-panel').removeClass('active');
        $(this).addClass('active');
        $('.affos-tab-panel[data-panel="' + tab + '"]').addClass('active');
    });

    // Buy links management
    var rowIndex = (typeof affosProductMeta !== 'undefined') ? affosProductMeta.rowIndex : 0;
    var storeOptions = (typeof affosProductMetaL10n !== 'undefined') ? affosProductMetaL10n.storeOptions : {};
    var pricePlaceholder = (typeof affosProductMetaL10n !== 'undefined') ? affosProductMetaL10n.pricePlaceholder : '';

    $('#add-buy-link').on('click', function () {
        var optionsHtml = '';
        $.each(storeOptions, function (key, label) {
            optionsHtml += '<option value="' + key + '">' + label + '</option>';
        });

        var row = '<tr>' +
            '<td><select name="_product_buy_links[' + rowIndex + '][store_name]" class="regular-text" style="width: 100%;">' + optionsHtml + '</select></td>' +
            '<td><input type="url" name="_product_buy_links[' + rowIndex + '][store_url]" class="regular-text" placeholder="https://" style="width: 100%;" /></td>' +
            '<td><input type="text" name="_product_buy_links[' + rowIndex + '][store_price]" class="regular-text" placeholder="' + pricePlaceholder + '" style="width: 100%;" /></td>' +
            '<td><button type="button" class="button remove-buy-link"><span class="dashicons dashicons-trash"></span></button></td>' +
            '</tr>';
        $('#buy-links-body').append(row);
        rowIndex++;
    });

    $(document).on('click', '.remove-buy-link', function () {
        $(this).closest('tr').remove();
    });
});

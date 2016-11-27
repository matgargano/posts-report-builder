var jQuery = require('jquery');

jQuery(document).ready(function ($) {
    $('.verify').on('submit', function (e) {
        var confirmMsg = 'Are you sure you wish to continue?';
        if ($(this).attr('data-custom-confirm')) {
            confirmMsg = $(this).attr('data-custom-confirm');
        }
        if (confirm(confirmMsg)) {
            return true;
        }
        e.preventDefault();
    });
});
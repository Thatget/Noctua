require([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal) {

    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        buttons: false,
        modalClass: 'sit-360-modal'
    };
    $(".sit-product-360-view span").on('click', function() {
        var id = $(this).attr('data-id');
        var proSku = $(this).attr('id');

           request = $.ajax({
            url: $('#basurl-withoutcode').val()+"webrotate360",
            type: "get",
            data: {
                'id': proSku
            }
        });
        request.done(function(response) {
            if (response) {
                $('#' + id).html(response);
            }
        });
        var popup = modal(options, $('#' + id));
        $('#' + id).modal("openModal");
    });

    $('.copy-button').on('click', function() {
        var textareaId = $(this).parent('.sit-360-copy-button').siblings('textarea').attr('id');
        var copyText = $('#' + textareaId).select();
        document.execCommand('copy');
        $('.copy-button').val('Copy');
        $(this).val('Copied');
        setTimeout(function() {
            $('.copy-button').val('Copy');
        }, 5000);
    });
});
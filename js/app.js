jQuery(document).ready(function ($) {
    $("#h2p").click(function (event) {
        $("#h2p button").prop('disabled', true);
        $("<img src=\"" + wp_ajax.load + "\" style=\"margin-left: 10px;\">").appendTo("#h2p");
        var data = {
            action: "get_pdf",
            security: wp_ajax.ajaxnonce,
            filename: wp_ajax.filename,
            params: wp_ajax.params,
        };
        $.post(
            wp_ajax.ajaxurl,
            data,
            function (msg) {
                var link = document.createElement('a');
                link.href = msg;
                link.download = wp_ajax.filename;
                link.dispatchEvent(new MouseEvent('click'));
                $("#h2p button").prop('disabled', false);
                $("#h2p img").remove();
            });
        event.preventDefault();
    });
});

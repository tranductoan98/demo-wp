jQuery(document).ready(function ($) {

    var $wwp_getting_started = $(".wwp-getting-started");

    $wwp_getting_started.find('button.notice-dismiss').click(function (e) {

        $wwp_getting_started.fadeOut("fast", function () {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "wwp_getting_started_notice_hide"
                },
                dataType: "json"
            })
                .done(function (data, textStatus, jqXHR) {
                    // notice is now hidden
                })

        });

    });

});
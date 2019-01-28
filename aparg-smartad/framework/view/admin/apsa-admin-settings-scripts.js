jQuery(document).ready(function ($) {
    /*
     * set CodeMirror textareas values
     */
    var code_areas = document.getElementsByClassName("apsa-code-area");
    var myCodeMirrors = {};
    myCodeMirrors.customCss = CodeMirror.fromTextArea(code_areas[0], {
        lineNumbers: true,
        mode: "htmlmixed",
    });
    myCodeMirrors.customCss.setValue($('#apsa-custom-css-value').val());

    myCodeMirrors.customCss.on('change', function () {
        apsa_leave_page(false);
    });

    /**
     * Close popup
     */
    $(document).on('click', '#apsa-managing-overlay, .apsa-close-popup', function () {
        $('.apsa-popup').attr('data-apsa-open', "false");
        jQuery('body').removeClass('modal-open');
        $('#apsa-managing-overlay').fadeOut(150);
    });

    /*
     *  save extra options 
     */
    $(document).on("click", "#apsa-update-settings", function () {
        apsa_leave_page(true);
    });

    /**
     * close anticache notice
     */
    $(document).on("click", ".apsa-dismissible", function () {
        $(this).parent('div').hide();
        apsa_set_cookie('apsa_anticache_notice', 'no');
    });

    /*
     * Show a prompt before the user leaves the current page
     */

    $(window).load(function () {
        $(document).on('change', 'input, textarea, select', function () {
            apsa_leave_page(false);
        });
    });

    // desable apsa-new class 
    if (typeof apsa_new == 'undefined') {
        $('.apsa-new').each(function () {
            $(this).addClass('apsa-new-show');
        });
    }

});
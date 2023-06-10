jQuery(document).ready(function($) {
    $('.tab-panel').hide();
    $('.nav-tab-active').show()
    $('.nav-tab').click(function(e) {
        e.preventDefault();

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-panel').hide();
        $($(this).attr('href')).show();
    });
});
jQuery(document).ready(function($) {
    $('.tab-panel').hide();
    $($('.nav-tab-active').attr('href')).show();

    $('.nav-tab').click(function(e) {
        e.preventDefault();

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-panel').hide();
        $($(this).attr('href')).show();
    });

    var tableContainer = $(".file-types-table-container");
    var tableRows = $(".wp-list-table tbody tr");
    var toggleButton = $("#toggle-button");

    toggleButton.on("click", function() {
        tableContainer.toggleClass("show-all");

        if (tableContainer.hasClass("show-all")) {
            toggleButton.text("Show Less");
            tableRows.removeClass("hidden");
        } else {
            toggleButton.text("Show More");
            tableRows.slice(5).addClass("hidden");
        }
    });
});

jQuery(document).ready(function($) {
    $('#progress-bar').hide();
    $('#gather-results-btn').on('click', function() {
        $(this).addClass('disabled').attr('disabled', 'disabled');
        updateProgressBar(0,100)
        scanFile()
    });

    function scanFile(progress) {
        // Show the progress bar and text
        if ( !progress || progress == 0 ) {
            $('#progress-bar').show();
            $('#progress-text').text('Scanning...');
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'gather_disk_usage_results',
                progress: progress ?? 0// Replace with your AJAX action name
            },
            success: function(response) {
                if (response.progress < response.total ){
                    var percentage = (response.progress / response.total) * 100;
                    updateProgressBar(response.progress, response.total);
                    $('#progress-text').text('Scanning: ' + percentage.toFixed(0) + '%');
                    setTimeout( function (){
                        scanFile(response.progress)
                    }, 1000)
                }else {
                    // Scanning complete
                    updateProgressBar(100, 100);
                    $('#progress-text').text('Scanning complete');

                    $('.nav-tab').removeClass('nav-tab-active');
                    $('.nav-tab:first').addClass('nav-tab-active');
                    $('.tab-panel').hide();
                    $($('.nav-tab:first').attr('href')).show();
                    setTimeout(function (){
                        $('#progress-bar').hide()
                        $('#gather-results-btn').removeClass('disabled').removeAttr('disabled');
                    }, 3000)
                }
                // generateFileTree(response, $('#file-tree'));
            },
            error: function(xhr, status, error) {
                // Handle the error case
                console.error(error);
            },
            complete: function() {
            }
        });
    }

    function updateProgressBar(progress, total) {
        var percentage = (progress / total) * 100;
        $('#progress-bar-fill').css('width', percentage + '%');
    }
});

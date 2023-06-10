jQuery(document).ready(function($) {
    $('#gather-results-btn').on('click', function() {
        // Show a loading indicator or progress bar
        // Add your code to display the data gathering process window or portion of the screen

        // Perform AJAX calls to gather the results
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'gather_disk_usage_results' // Replace with your AJAX action name
            },
            success: function(response) {
                // Update the Results Section with the gathered usage stats
                // Add your code to update the Results Section with the response data
            },
            error: function(xhr, status, error) {
                // Handle the error case
                console.error(error);
            },
            complete: function() {
                // Hide the loading indicator or progress bar
                // Add your code to hide the data gathering process window or portion of the screen
            }
        });
    });
});

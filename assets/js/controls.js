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
                generateFileTree(response.directories, $('#file-tree'));
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

    // Traverse the file structure and generate the HTML file tree
    function generateFileTree(data, parent) {
            var ul = $('<ul>');

            // Loop through directories
            if (data.directories) {
                $.each(data.directories, function(index, directory) {
                    var li = $('<li>').text(directory.path);
                    ul.append(li);

                    // Recursively build the file tree for subdirectories
                    if (directory.subfolders) {
                        generateFileTree(directory, li);
                    }
                });
            }

            // Loop through files
            if (data.files) {
                $.each(data.files, function(index, file) {
                    var li = $('<li>').text(file.path);
                    ul.append(li);
                });
            }
            parent.append(ul);
    }

});



// $(document).ready(function() {
//
// });
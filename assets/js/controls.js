jQuery(document).ready(function($) {
    $('#gather-results-btn').on('click', function() {
        scanFile()
    });

    function scanFile(progress)
    {
        console.log(progress)
        // Show a loading indicator or progress bar
        // Add your code to display the data gathering process window or portion of the screen

        // Perform AJAX calls to gather the results
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
                    console.log(response.progress)
                    scanFile(response.progress)
                }
                // generateFileTree(response, $('#file-tree'));
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
    }

    // Traverse the file structure and generate the HTML file tree
    function generateFileTree(data, parent) {
        var ul = $('<ul>');

        // Loop through directories
        if (data.directories) {
            $.each(data.directories, function(index, directory) {
                var li = $('<li class="directory folder">').text(directory.name);

                // Display file count and subdirectory count for directories
                var count = directory.files.length + directory.subfolders.length;
                li.append(' (' + count + ' items)');

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
                var li = $('<li class="file">').text(file.name);

                // Display file size for files
                li.append(' (' + file.size + ' bytes)');

                ul.append(li);
            });
        }
        parent.append(ul);
    }


});



// $(document)
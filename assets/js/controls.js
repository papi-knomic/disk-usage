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
                    setTimeout(function (){
                        $('#progress-bar').hide()
                        $(this).removeClass('disabled').removeAttr('disabled');
                    }, 5000)
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
<table id="files-treegrid" class="treegrid table table-hover table-sm small text-end mb-4">
    <thead class="table-light">
    <tr>
        <th class="text-start">Name</th>
        <th>Subtree Percent</th>
        <th>Percent</th>
        <th>Size</th>
		<?php if (!is_windows_os()) { ?>
            <th>SizeOnDisk</th>
		<?php } ?>
        <th>Items</th>
        <th>Files</th>
        <th>Subdirs</th>
    </tr>
    </thead>
    <tbody>
    <?php
        function generateTableRows($nodes, $depth = 0)
        {
            $progressBgColors = array('bg-info', 'bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-secondary');
            foreach ($nodes as $node) {
                $name = $node['name'];
                $size = $node['size'];
                $children = $node['children'];

                echo '<tr class="treegrid-' . $node['id'] . ' ';
                if ($node['parent']) {
                    echo 'treegrid-parent-' . $node['parent']['id'] . ' ';
                    foreach ($node['parentsIds'] as $parentId) {
                        echo 'treegrid-collapse-' . $parentId . ' ';
                    }
                }
                if ($node['depth'] == 0 || $node['depth'] == 1) {
                    echo 'expanded ';
                }
                if ($node['error']) {
                    echo 'table-danger';
                }
                echo '">';

                echo '<td class="text-start" style="padding-left:' . (28 + $depth * 21) . 'px">';
                if ($node['isDir'] && $node['itemsCount'] > 0) {
                    echo '<i class="treegrid-expander ' . ($node['id'] == 0 ? 'bi-caret-down-fill' : 'bi-caret-right-fill') . '"></i>';
                }
                echo '<i class="' . ($node['isDir'] ? 'bi-folder-fill text-warning' : 'bi-file-text-fill text-muted') . '"></i>';
                echo $name;
                echo '</td>';

                echo '<td>';
                echo '<div class="progress" ' . ($node['depth'] > 1 ? 'style="margin-left:' . (($node['depth'] - 1) * 10) . 'px"' : '') . '>';
                echo '<div class="progress-bar ' . ($node['depth'] > 0 ? $progressBgColors[($node['depth'] - 1) % sizeof($progressBgColors)] : 'bg-info') . '" role="progressbar" style="width: ' . ceil($node['percent']) . '%" aria-valuenow="' . ceil($node['percent']) . '" aria-valuemin="0" aria-valuemax="100"></div>';
                echo '</div>';
                echo '</td>';

                echo '<td>' . round($node['percent'], 2) . ' %</td>';
                echo '<td>' . formatBytes($size) . '</td>';
           if (!is_windows_os()) {
               echo '<td>' . formatBytes($node['sizeOnDisk']) . '</td>';
           }
    echo '<td>' . ($node['isDir'] ? $node['itemsCount'] : '') . '</td>';
    echo '<td>' . ($node['isDir'] ? $node['filesCount'] : '') . '</td>';
    echo '<td>' . ($node['isDir'] ? $node['subdirsCount'] : '') . '</td>';
    echo '</tr>';

    if (!empty($children)) {
    generateTableRows($children, $depth + 1); // Recursive call for children
        }
            }
        }

    generateTableRows($files);
    ?>
    </tbody>
</table>

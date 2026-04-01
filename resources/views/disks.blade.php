<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>Disks</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px; margin-bottom:20px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 ">
                <h2 class="font-semibold text-xl  leading-tight nav-div-alt headerfix">
                    Disks
                </h2>
            </div>
        </header>
        @include('includes.response-handling')

        <div class="py-12">
            <div class="p-4 sm:p-8  theme-divBg shadow sm:rounded-lg">
                <?php
                $filename = storage_path('app/private/disks.csv');
                $file = fopen($filename, 'r');

                $all_data = [];

                // Get the header row first
                $headers = fgetcsv($file, null, ",");

                while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
                    // Combine headers with row values
                    $row = array_combine($headers, $data);

                    $all_data[] = $row;

                    // print_r($row);
                }

                fclose($file);

                ?>

                <?php if (!empty($all_data)): ?>

                <table id="disks" class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead>
                        <tr class="theme-tableOuter align-middle text-center">
                            <?php foreach (array_keys($all_data[0]) as $heading): ?>
                                <th><?php echo htmlspecialchars($heading); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($all_data as $row): ?>
                            <tr class="align-middle text-center">
                                <?php foreach ($row as $cell): ?>
                                    <td><?php echo htmlspecialchars($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            
                

<?php endif; ?>
            @dd(get_defined_vars())
        </div>
        </div>

    </div>

    @include('foot')
</body>

<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>Stock Import</title>
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

        <div class="container"  style="padding-top:25px">
            <table class="table-auto w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Vendor</th>
                        <th class="px-4 py-2">Model</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Size</th>
                        <th class="px-4 py-2">Speed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disk_items['rows'] as $disk)
                    <tr>
                        <td class="border px-4 py-2">{{ $disk['id'] }}</td>
                        <td class="border px-4 py-2">{{ $disk_vendors['rows'][$disk['vendor_id']] }}</td>
                        <td class="border px-4 py-2">{{ $disk['disk_model'] }}</td>
                        <td class="border px-4 py-2">{{ $disk_types['rows'][$disk['type_id']] }}</td>
                        <td class="border px-4 py-2">{{ $disk_capacities['rows'][$disk['capacity_id']] }}</td>
                        <td class="border px-4 py-2">{{ $disk_speeds['rows'][$disk['speed_id']] }}</td>
                    </tr>
                    @endforeach
                </tbody>  
                
            </table>
            @dd(get_defined_vars())
        </div>

    </div>

    @include('foot')
</body>

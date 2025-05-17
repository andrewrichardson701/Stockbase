<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}}</title>
</head>
<body>

    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="min-h-screen">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px; margin-bottom:20px">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight headerfix">
                    Assets
                </h2>
            </div>
        </header>
        {!! $response_handling !!}
        
        <div class="container">
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg @if (in_array($head_data['user']['role_id'], [1, 3]))  clickable" onclick="navPage(`{{ url('optics') }}`)" @else  no-perms" title="Not permitted" @endif
                style="margin:5px">
                    <h4>Optics</h4>
                    <img style="max-width:100px;overflow:hidden;" src="/img/stock/SFP.png">
                </div>
                <div class="col text-center well-nopad theme-divBg clickable" style="margin:5px" onclick="navPage(`{{ url('cpus') }}`)">
                    <h4>CPUs</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/stock/CPU.png">
                </div>
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage(`{{ url('memory') }}`)" title="Coming soon...">
                    <h4>Memory</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/stock/RAM.png">
                </div>
            </div>
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage(`{{ url('disks') }}`)" title="Coming soon...">
                    <h4>Disks</h4>
                    <img style="max-width:100px;overflow:hidden;" src="/img/stock/HDD.png">
                </div>
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage(`{{ url('fans') }}`)" title="Coming soon...">
                    <h4>Fans</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/stock/Fan.png">
                </div>
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage(`{{ url('psus') }}`)" title="Coming soon...">
                    <h4>PSUs</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/stock/PSU.png">
                </div>
            </div>
        </div>
        
        <?php
        ?>

    </div>
    

    <!-- Add the JS for the file -->
    <!-- <script src="assets/js/assets.js"></script> -->

    @include('foot')
</body>

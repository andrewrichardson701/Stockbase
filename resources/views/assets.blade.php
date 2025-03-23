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

    <div class="content">
        {!! $response_handling !!}
        <div class="container">
            <h2 class="header-small" style="padding-bottom:5px">Assets</h2>
        </div>

        <div class="container">
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg @if (in_array($head_data['user']['role_id'], [0, 2]))  clickable" onclick="navPage(`{{ url('optics') }}`)" @else  no-perms" title="Not permitted" @endif
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

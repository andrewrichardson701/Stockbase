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

    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl  leading-tight headerfix">
                    Assets
                </h2>
            </div>
        </header>
        @include('includes.response-handling')
        
        <div class="container" style="margin-top:20px">
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['optics'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('optics') }}`)">
                    <h4>Optics</h4>
                    <img style="max-width:100px;overflow:hidden;" src="/img/assets/SFP.png">
                </div>
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['cpus'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('cpus') }}`)">
                    <h4>CPUs</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/assets/CPU.png">
                </div>
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['memory'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('memory') }}`)">
                    <h4>Memory</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/assets/RAM.png">
                </div>
            </div>
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['disks'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('disks') }}`)">
                    <h4>Disks</h4>
                    <img style="max-width:100px;overflow:hidden;" src="/img/assets/HDD.png">
                </div>
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['fans'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('fans') }}`)">
                    <h4>Fans</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/assets/Fan.png">
                </div>
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['psus'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('psus') }}`)">
                    <h4>PSUs</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="/img/assets/PSU.png">
                </div>
            </div>
        </div>
        
        <?php
        ?>

    </div>
    

    <!-- Add the JS for the file -->
    <!-- <script src={{ asset('js/assets.js') }}></script> -->

    @include('foot')
</body>

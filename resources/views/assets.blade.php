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
        
        <div class="container" style="margin-top:10px">
            <div class="viewport-font">
                Total Asset Count: 
                <or class="title @if($assets['all']['count'] > 0) green @else red @endif" title="Optics: {{ $assets['optics']['count'] }}, CPUs: {{ $assets['cpus']['count'] }}, Memory: {{ $assets['memory']['count'] }}, Disks: {{ $assets['disks']['count'] }}">
                    {{ $assets['all']['count'] }}
                </or>
            </div>

            <div class="row">
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['optics'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('optics') }}`)" style="max-height:170px" >
                    <h4 style="padding-bottom:10px">Optics</h4>
                    <img style="max-width:150px;max-height:100px;overflow:hidden;margin:auto;" src="/img/assets/SFP.png">
                </div>
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['cpus'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('cpus') }}`)" style="max-height:170px" >
                    <h4 style="padding-bottom:10px">CPUs</h4> 
                    <img style="max-width:150px;max-height:100px;overflow:hidden;margin:auto;" src="/img/assets/CPU.png">
                </div>
            </div>
            <div class="row">
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['memory'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('memory') }}`)" style="max-height:170px" >
                    <h4 style="padding-bottom:10px">Memory</h4>
                    <img style="max-width:150px;max-height:100px;overflow:hidden;margin:auto;height:max-content" src="/img/assets/RAM.png">
                </div>
                <div class="col text-center well-nopad theme-divBg @if($head_data['user']['permissions']['disks'] == 0) no-perms" disabled title="No permission. @else clickable @endif" style="margin:5px" onclick="navPage(`{{ route('disks') }}`)" style="max-height:170px" >
                    <h4 style="padding-bottom:10px">Disks</h4>
                    <img style="max-width:150px;max-height:100px;overflow:hidden;margin:auto;height:max-content" src="/img/assets/HDD.png">
                </div>
            </div>
        </div>
        
        <?php
        ?>

    </div>

    @include('foot')
</body>

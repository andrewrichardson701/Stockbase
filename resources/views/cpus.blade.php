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
                    CPUs
                </h2>
            </div>
        </header>
        {!! $response_handling !!}

        Page in progress...
    </div>

    <!-- Add the JS for the file -->
    <!-- <script src="{{ asset('js/cpus.js') }}"></script> -->

    @include('foot')
</body>
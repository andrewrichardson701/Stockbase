<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - About</title>
</head>
<body>
    @include('nav')
    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl  leading-tight headerfix">
                    About
                </h2>
            </div>
        </header>
        <div class="container" style="margin-top:25px;">
            <div class="row">
                <div class="col">
                    <h3 style="font-size:22px">StockBase ({{$head_data['version_number']}})</h3>
                    <div style="padding-top: 20px;">
                        <p>StockBase, an inventory and stock system, with less of the <i>bloat</i>. </p>
                        <p style="margin-top:30px">{{$head_data['config_compare']['system_name']}} is powered by StockBase, an open source, minimalist stock management system.<br>
                        Learn more at the <a href="https://github.com/andrewrichardson701/stockbase">GitHub page</a>.</p>
                        <p>StockBase is licenced under the <a href="https://www.gnu.org/licenses/gpl-3.0.txt">GNU GPL licence</a>.</p>
                        <p>StockBase Copyright © {{ now()->year }} Andrew Richardson. All rights reserved.</p>
                    </div>
                </div>
                <div class="col-1"></div>
                <div class="col-4 changelog" id="version-changelog" style="max-height:60vh;overflow-x: hidden;overflow-y: auto; ">
                    {!! file_get_contents(public_path('CHANGELOG.md')) !!}
                </div>
            </div>
        </div>
    </div>
        
@include('foot')

</body>

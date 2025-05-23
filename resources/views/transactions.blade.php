<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Transactions</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl leading-tight headerfix">
                    Transactions
                </h2>
            </div>
        </header>
        <div class="container">
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:5px">
                    @if(isset($stock_data['name'])) 
                    <a class="link" href="{{ url('stock') }}/{{ $params['stock_id'] }}">{{ $stock_data['name'] }}</a> - Stock ID: {{ $params['stock_id'] }} @if ($stock_data['is_cable'] == 1)  (cable)@endif 
                    @else
                    All Stock
                    @endif
                </h2>
            </div>
            @include('includes.transactions')
        </div>
    </div>
    
    @include('foot')
</body>

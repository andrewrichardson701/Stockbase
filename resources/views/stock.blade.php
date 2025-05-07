<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="content">
        @include('includes.stock.new-properties')

        @if(!is_numeric($params['stock_id']))
            @if(!empty($params['modify_type']))
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter('', 'stock_id', 0))">add new stock item</a>.</p></div>
            @else
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or go back to the <a class="link" href="{{ url('/') }}">home page</a>.</p></div>
            @endif
        @endif

        <!-- Get Inventory -->
        @if(!empty($params['modify_type']))
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:5px">Stock - {{ ucwords($params['modify_type']) }}</h2>
                {!! $response_handling !!}
            </div>
            @include('includes.stock.' . $params['modify_type'])
        @else
            @include('includes.stock.view')
        @endif

    </div>

    @include('includes.stock.modals')
     
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/stock.js') }}"></script>

    @include('foot')
</body>

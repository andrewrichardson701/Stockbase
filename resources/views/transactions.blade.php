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

    <div class="content">
        <div class="container" style="padding-bottom:25px">
            <h2 class="header-small" style="padding-bottom:5px">Transactions - <a class="link" href="{{ url('stock') }}/{{ $params['stock_id'] }}">{{ $stock_data['name'] }}</a> - Stock ID: {{ $params['stock_id'] }} @if ($stock_data['is_cable'] == 1)  (cable)@endif</h2>
        </div>
        <div class="container">
            @include('includes.stock.transactions')
        </div>
    </div>
    
    @include('foot')
</body>

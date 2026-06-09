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
        <div class="container" style="padding-bottom:25px">
            <h2 class="header-small" style="padding-bottom:5px">
                @if(isset($stock_data['name'])) 
                <a class="link" href="{{ url('stock') }}/{{ $params['stock_id'] }}">{{ $stock_data['name'] }}</a> - Stock ID: {{ $params['stock_id'] }} @if ($stock_data['is_cable'] == 1)  (cable)@endif 
                @else
                All Transactions
                @endif
            </h2>
        </div>
        <div id="transactions-table" class="text-center" style="max-width:max-content; margin:auto">
            <table class="table table-dark theme-table centertable" id="cableSelection" style="border:0px !important">
                <thead class="theme-tableOuter" style="text-align: center; white-space: nowrap; border:0px !important">
                    <tr style="border:0px !important">
                        <th class="clickable @if ($params['type'] == 'stock' || $params['type'] == '') theme-th-selected @else th-noBorder @endif " onclick="window.location='{{ route('transactions', ['type' => 'stock', 'stock_id' => null]) }}'">Stock</th>
                        <th class="clickable @if ($params['type'] == 'cables') theme-th-selected @else th-noBorder @endif " onclick="window.location='{{ route('transactions', ['type' => 'cables', 'stock_id' => null]) }}'">Cables</th>
                        <th class="clickable @if ($params['type'] == 'optics') theme-th-selected @else th-noBorder @endif " onclick="window.location='{{ route('transactions', ['type' => 'optics', 'stock_id' => null]) }}'">Optics</th>
                        <th class="clickable @if ($params['type'] == 'cpus') theme-th-selected @else th-noBorder @endif " onclick="window.location='{{ route('transactions', ['type' => 'cpus', 'stock_id' => null]) }}'">CPUs</th>
                        <th class="clickable @if ($params['type'] == 'memory') theme-th-selected @else th-noBorder @endif " onclick="window.location='{{ route('transactions', ['type' => 'memory', 'stock_id' => null]) }}'">Memory</th>
                        <th class="clickable @if ($params['type'] == 'disks') theme-th-selected @else th-noBorder @endif " onclick="window.location='{{ route('transactions', ['type' => 'disks', 'stock_id' => null]) }}'">Disks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan=100% class="theme-th-selected">
                            @if ($params['type'] == 'stock') 
                                @include('includes.transactions.stock')
                            @elseif ($params['type'] == 'cables')
                                @include('includes.transactions.stock')
                            @elseif ($params['type'] == 'optics') 
                                @include('includes.transactions.optics')
                            @elseif ($params['type'] == 'disks') 
                                @include('includes.transactions.disks')
                            @elseif ($params['type'] == 'memory') 
                                @include('includes.transactions.memory')
                            @elseif ($params['type'] == 'cpus') 
                                @include('includes.transactions.cpus')
                            @else
                                <p class="red">Nothing to display.</p>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    @include('foot')
</body>

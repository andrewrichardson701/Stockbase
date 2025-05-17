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

    <div class="min-h-screen">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px; margin-bottom:20px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 ">
                <h2 class="font-semibold text-xl  leading-tight nav-div-alt headerfix">
                    Stock @if(isset($params['modify_type'])) &nbsp; - &nbsp; <or 
                        @if ($params['modify_type'] == 'remove') style="color:#dc3545"
                        @elseif($params['modify_type'] == 'add') style="color:#218838"
                        @elseif($params['modify_type'] == 'move') style="color:#e0a800"
                        @elseif($params['modify_type'] == 'edit') style="color:#17a2b8"
                        @endif
                        >{{ ucwords($params['modify_type']) }}</or>
                    @endif
                </h2>
                @if (isset($params['stock_id']) && $params['stock_id'] > 0)
                    @if (!isset($params['modify_type']))
                        <div class="col nav-div nav-right" style="max-width:max-content; width:max-content;margin-right:0px !important">
                            <div class="nav-row">
                                <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                    <button id="edit-stock" class="btn btn-info theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('{{ url('stock') }}/{{ $params['stock_id'] }}/edit')">
                                        <i class="fa fa-pencil"></i><or class="viewport-large-empty"> Edit</or>
                                    </button>
                                </div> 
                                <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                    <button id="add-stock" class="btn btn-success theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('{{ url('stock') }}/{{ $params['stock_id'] }}/add')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                        <i class="fa fa-plus"></i><or class="viewport-large-empty"> Add</or>
                                    </button>
                                </div> 
                                <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                    <button id="remove-stock" class="btn btn-danger theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('{{ url('stock') }}/{{ $params['stock_id'] }}/remove')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                        <i class="fa fa-minus"></i><or class="viewport-large-empty"> Remove</or>
                                    </button>
                                </div> 
                                <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                    <button id="transfer-stock" class="btn btn-warning nav-v-b stock-modifyBtn" style="color:black" onclick="navPage('{{ url('stock') }}/{{ $params['stock_id'] }}/move')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                        <i class="fa fa-arrows-h"></i><or class="viewport-large-empty"> Move</or>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </header>
        {!! $response_handling !!}
    
        @if(!is_numeric($params['stock_id']))
            @if(!empty($params['modify_type']))
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter('', 'stock_id', 0))">add new stock item</a>.</p></div>
            @else
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or go back to the <a class="link" href="{{ url('/') }}">home page</a>.</p></div>
            @endif
        @endif

        @include('includes.stock.new-properties')

        <!-- Get Inventory -->
        @if(!empty($params['modify_type']))
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

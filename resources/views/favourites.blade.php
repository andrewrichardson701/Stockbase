<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Favourites</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="content">
        <div class="container">
            <h2 class="header-small" style="padding-bottom:5px">
                <!-- <button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href='{{ url()->previous() }}'">
                    <i class="fa fa-chevron-left"></i> Back
                </button> -->
                Favourites
            </h2>
        </div>
        {!! $response_handling !!}
        <div style="padding-bottom:75px">
        @if ($favourites['count'] > 0 && !empty($favourites['rows']))
            <table class="table table-dark theme-table centertable" style="max-width:max-content;margin-bottom:0px;">
                <thead style="text-align: center; white-space: nowrap;">
                    <tr class="theme-tableOuter align-middle text-center">
                        <th></th>
                        <th class="align-middle">ID</th>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">SKU</th>
                        <th class="align-middle">Area(s)</th>
                        <th class="align-middle">Tags</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($favourites['rows'] as $row)
                    @if (!empty($row['stock_data']) && !empty($row['img_data']['rows']) && isset($row['stock_data']['id']))
                    <tr id="stock-{{ $row['stock_data']['id'] }}">
                        <td class="text-center align-middle">
                            @if ($row['img_data']['count'] > 0 && !empty($row['img_data']['rows'][0]))
                            <img id="image-{{ $row['stock_data']['id'] }}" class="inv-img-main thumb" style="cursor:default !important" src="{{ asset('img/stock/'. $row['img_data']['rows'][0]['image']) }}">
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $row['stock_data']['id'] }}</td>
                        <td id="stock-{{ $row['stock_data']['id'] }}-name" class="text-center align-middle" style="width:300px"><a class="link" href="{{ url('stock') }}/{{ $row['stock_data']['id'] }}">{{ $row['stock_data']['name'] }}</a></td>
                        <td class="text-center align-middle">{{ $row['stock_data']['sku'] }}</td>
                        <td class="text-center align-middle">
                        @if ($row['area_data']['count'] > 0 && !empty($row['area_data']['rows']))
                            @foreach($row['area_data']['rows'] as $area)
                            <or id="stock-{{ $row['stock_data']['id'] }}-area-{{ $area['id'] }}" class="gold link" onclick="navPage(updateQueryParameter(`{{ url('/') }}`, 'area', `{{ $area['id'] }}`))">{{ $area['name'] }}</or>@if (!$loop->last), @endif
                            @endforeach
                        @endif
                        </td>
                        <td class="text-center align-middle">
                        @if ($row['tag_data']['count'] > 0 && !empty($row['tag_data']['rows']))
                            @foreach($row['tag_data']['rows'] as $tag)
                            <or id="stock-{{ $row['stock_data']['id'] }}-tag-{{ $tag['id'] }}" class="gold link" onclick="navPage(updateQueryParameter(`{{ url('/') }}`, 'tag', `{{ $tag['name'] }}`))">{{ $tag['name'] }}</or>@if(!$loop->last), @endif
                            @endforeach
                        @endif
                        </td>
                        <td class="text-center align-middle">
                            <button onclick="favouriteStockReload({{ $row['stock_data']['id'] }})" class="btn btn-danger" style="padding:3px 6px 3px 6px; color:black" title="Remove Favourite">
                                <i id="favouriteIcon" class="fa-regular fa-star"></i>
                            </button>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="100%" class="red">Missing Stock Data</td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @else
            <p class="container red" style="margin-top:20px">No favourites found.</p> 
        @endif
        </div>

    </div>
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/favourites.js') }}"></script>

    @include('foot')
</body>
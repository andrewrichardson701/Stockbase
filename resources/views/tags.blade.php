<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Tags</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <div class="min-h-screen">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl leading-tight headerfix">
                    @if (isset($previous_url))
                    {{-- <button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href='{{ $previous_url }}'"><i class="fa fa-chevron-left"></i> Back</button> --}}
                    @endif
                    Tags
                </h2>
            </div>
        </header>

        {!! $response_handling !!}

        <div class="container" style="margin-top:20px">
        @if ($tag_data['count'] > 0)
            <table class="table table-dark theme-table centertable" style="margin-bottom:0px;">
                <thead style="text-align: center; white-space: nowrap;">
                    <tr class="theme-tableOuter align-middle">
                        <th class="align-middle">ID</th>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Description</th>
                        <th class="align-middle">Objects</th>
                        <th colspan=2 class="align-middle"><button type="button" style="padding: 3px 6px 3px 6px" class="btn btn-success" onclick="modalLoadProperties('tag')">+ Add New</button></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tag_data['rows'] as $tag)
                    <tr id="tag-{{ $tag['id'] }}">
                        <td class="text-center align-middle">{{ $tag['id'] }}</td>
                        <td class="text-center align-middle" style="width:300px">{{ $tag['name'] }}</td>
                        <td class="text-center align-middle">{{ $tag['description'] }}</td>
                        <td class="text-center align-middle @if ($tag['stock_data']['count'] == 0) red @endif">{{ $tag['stock_data']['count'] }}</td>
                        <td class="text-center align-middle"><button class="btn btn-info" name="submit" title="Edit" onclick="toggleEditTag('{{ $tag['id'] }}')"><i class="fa fa-pencil"></i></td>
                        @if ($tag['stock_data']['count'] > 0) 
                        <th class="text-center align-middle clickable" style="width:50px" id="tag-{{ $tag['id'] }}-toggle" onclick="toggleHiddenTag('{{ $tag['id'] }}')">+</th>
                        @else
                        <th class="text-center align-middle" style="width:50px" id="tag-{{ $tag['id'] }}-toggle">&nbsp;</th>
                        @endif
                    </tr>
                    <tr id="tag-{{ $tag['id'] }}-edit" hidden>
                        <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                            <!-- Include CSRF token in the form -->
                            @csrf
                            <input type="hidden" name="tag_edit_submit" value="1" />
                            <input type="hidden" name="tag_id" value="{{ $tag['id'] }}" />
                            <td class="text-center align-middle">{{ $tag['id'] }}</td>
                            <td class="text-center align-middle" style="width:300px"><input type="text" class="form-control text-center" style="max-width:100%" name="tag_name" value="{{ htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') }}"></td>
                            <td class="text-center align-middle"><input type="text" class="form-control text-center" style="max-width:100%" name="tag_description" value="{{ htmlspecialchars($tag['description'], ENT_QUOTES, 'UTF-8') }}"></td>
                            <td class="text-center align-middle @if ($tag['stock_data']['count'] == 0) red @endif">{{ $tag['stock_data']['count'] }}</td>
                            <td class="text-center align-middle" style=""><span><button class="btn btn-success" title="Save" style="margin-right:10px" name="submit"><i class="fa fa-save"></i></button><button type="button" class="btn btn-warning" name="submit" style="padding:3px 12px 3px 12px" onclick="toggleEditTag('{{ $tag['id'] }}')">Cancel</button></span></td>
                            @if ($tag['stock_data']['count'] > 0) 
                            <th class="text-center align-middle clickable" style="width:50px" id="tag-{{ $tag['id'] }}-edit-toggle" onclick="toggleHiddenTag('{{ $tag['id'] }}')">+</th>
                            @else
                            <th class="text-center align-middle" style="width:50px" id="tag-{{ $tag['id'] }}-edit-toggle">&nbsp;</th>
                            @endif
                        </form>
                    </tr>
                    @if ($tag['stock_data']['count'] > 0)
                        <tr id="tag-{{ $tag['id'] }}-stock" hidden>
                            <td colspan=100%>
                                <div style="margin: 5px 20px 10px 20px">
                                    <table class="table table-dark theme-table centertable" style="margin:0px;max-width:100%;border: 1px solid #454d55;">
                                        <thead style="text-align: center; white-space: nowrap;">
                                            <tr class="theme-tableOuter">
                                                <th></th>
                                                <th>Stock ID</th>
                                                <th>Stock Name</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tag['stock_data']['rows'] as $row)
                                            <tr id="tag-{{ $tag['id'] }}-stock-{{ $row['id'] }}">
                                                <td class="text-center align-middle">
                                                    @if (isset($row['img_data']['rows'][0]))
                                                    <img id="image-{{ $row['img_data']['rows'][0]['id'] }}" class="inv-img-main thumb" src="{{ asset('img/stock/'. $row['img_data']['rows'][0]['image']) }}">
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">{{ $row['id'] }}</td>
                                                <td class="text-center align-middle link"><a href="{{ url('stock') }}/{{ $row['id'] }}">{{ $row['name'] }}</a></td>
                                                <td class="text-center align-middle">{{ $row['quantity'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @else
            No Collections Found
        @endif
        </div>
        @include('includes.stock.new-properties')
    </div>
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/tags.js') }}"></script>
    @include('foot')
</body>
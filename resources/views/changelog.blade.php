<!DOCTYPE html>
<html lang="en">
    <head>
    
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $head_data['config_compare']['system_name'] }} - Changelog</title>
        @include('head')
        
    </head>
    <body class="font-sans antialiased">
        @include('nav')
        <div class="min-h-screen">
            <!-- Page Heading -->
            <header class="theme-divBg shadow" style="padding-top:60px">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Changelog
                    </h2>
                </div>
            </header>
            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
                        <div class="text-center p-4 sm:p-8  theme-divBg shadow sm:rounded-lg">
                            <form action="{{ route('changelog.filter') }}" method="POST" class="text-center centertable" style="max-width:max-content">
                                @csrf
                                <div class="row" style="max-width:max-content">
                                    <div class="col" style="max-width:max-content">
                                        <div class="row align-middle">
                                            <div class="col" style="max-width:max-content;margin-top:3px">
                                                <label class="nav-v-c">Start Date:</label>
                                            </div>
                                            <div class="col" style="max-width:max-content">
                                                <input class="form-control nav-v-c row-dropdown" type="date" name="start-date" value="{{ $params['start_date'] }}" style="width:max-content"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width:max-content">
                                        <div class="row align-middle">
                                            <div class="col" style="max-width:max-content;margin-top:3px">
                                                <label class="nav-v-c">End Date:</label>
                                            </div>
                                            <div class="col" style="max-width:max-content">
                                                <input class="form-control nav-v-c row-dropdown" type="date" name="end-date" value="{{ $params['end_date'] }}" style="width:max-content"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width:max-content">
                                        <div class="row align-middle">
                                            <div class="col" style="max-width:max-content;margin-top:3px">
                                                <label class="nav-v-c">Table:</label>
                                            </div>
                                            <div class="col" style="max-width:max-content">
                                                <select class="form-control nav-v-c row-dropdown" style="max-width:max-content; padding-right:35px" name="table">
                                                    <option value="all" @if ($params['table'] == null) selected @endif>All</option>
                                                    @if (!empty($tables))
                                                        @foreach ($tables as $table)
                                                        <option value={{ $table }} @if($params['table'] == $table) selected @endif>{{ $table }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width:max-content">
                                        <div class="row align-middle">
                                            <div class="col" style="max-width:max-content;margin-top:3px">
                                                <label class="nav-v-c">User:</label>
                                            </div>
                                            <div class="col" style="max-width:max-content">
                                                <select class="form-control nav-v-c row-dropdown" style="max-width:max-content; padding-right:35px" name="userid">
                                                    <option value="all" @if ($params['user'] == null) selected @endif>All</option>
                                                    @if ($users['count'] > 0)
                                                        @foreach ($users['rows'] as $user)
                                                        <option value={{ $user['id'] }} title="{{ $user['name'] }}" @if($params['user'] == $user['id']) selected @endif>{{ $user['username'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="max-width:max-content">
                                        <div class="col" style="max-width:max-content">
                                            <input class="form-control btn btn-info" type="submit" value="Filter" style="width:max-content"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="p-4 sm:p-8  theme-divBg shadow sm:rounded-lg">
                            <p class="container" style="margin-top:40px">Entry count: <or class="green">{{ $changelog['total_count'] }}</or></p>
                            <table id="changelogTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                                <thead>
                                    <tr class="theme-tableOuter align-middle text-center">
                                        <th>id</th>
                                        <th>timestamp</th>
                                        <th>user_id</th>
                                        <th>user_username</th>
                                        <th>action</th>
                                        <th>table_name</th>
                                        <th>record_id</th>
                                        <th>field_name</th>
                                        <th>value_old</th>
                                        <th>value_new</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if ($changelog['count'] > 0 && !empty($changelog['rows']))
                                    @foreach($changelog['rows'] as $log)
                                    <tr class="align-middle text-center clickable row-show" id="log-{{ $log['id'] }}" onclick="toggleHidden({{ $log['id'] }})">
                                        <td>{{ $log['id'] }}</td>
                                        <td>{{ $log['timestamp'] }}</td>
                                        <td>{{ $log['user_id'] }}</td>
                                        <td>{{ $log['user_username'] }}</td>
                                        <td>{{ $log['action'] }}</td>
                                        <td>{{ $log['table_name'] }}</td>
                                        <td>{{ $log['record_id'] }}</td>
                                        <td>{{ $log['field_name'] }}</td>
                                        <td>{{ $log['value_old'] }}</td>
                                        <td>{{ $log['value_new'] }}</td>
                                    </tr>
                                    
                                    <tr class="align-middle text-center row-hide" id="log-{{ $log['id'] }}-view" hidden>
                                        <td class="align-middle text-center" colspan=100%>
                                            <table class="centertable" style="width:100%">
                                            @if(is_array($log['info']))
                                                <thead>
                                                    <tr class="align-middle text-center">
                                                    @foreach(array_keys($log['info']) as $field)
                                                        <th>{{ $field }}</th>
                                                    @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="align-middle text-center">
                                                    @foreach(array_keys($log['info']) as $field)
                                                        <td>
                                                        @if ($field == 'stock_id' || ($log['table_name'] == 'stock' && $field == 'id'))
                                                            <a class="link" href="{{ route('stock', ['stock_id' => $log['info'][$field]]) }}">{{ $log['info'][$field] }}</a>
                                                        @else
                                                            {{ $log['info'][$field] }}
                                                        @endif
                                                        </td>
                                                    @endforeach
                                                    </tr>
                                                </tbody>
                                            @else
                                                <thead>
                                                    <tr>
                                                        <td>{{ $log['info'] }}</td>
                                                    </tr>
                                                </thead>
                                            @endif
                                            </table>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan=100% class="text-center">No entries found.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            <div class="container" style="text-align: center;">
                                @if ($params['pages'] > 1 && $params['pages'] <=15)
                                    @if ($params['page'] > 1)
                                        <or class="gold clickable" style="padding-right:2px" 
                                            onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $params['page']-1]) }}' + '')"><</or>
                                    @endif
                                    @if ($params['pages'] > 5)
                                        @for ($i = 1; $i <= $params['pages']; $i++)
                                            @if ($i == $params['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @elseif ($i == 1 && $params['page'] > 5)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $i]) }}' + '')">{{ $i }}</or><or style="padding-left:5px;padding-right:5px">...</or>
                                            @elseif ($i < $params['page'] && $i >= $params['page']-2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $i]) }}' + '')">{{ $i }}</or>
                                            @elseif ($i > $params['page'] && $i <= $params['page']+2)
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $i]) }}' + '')">{{ $i }}</or>
                                            @elseif ($i == $params['pages'])
                                                <or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $i]) }} + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @else
                                        @for ($i = 1; $i <= $params['pages']; $i++)
                                            @if ($i == $params['page'])
                                                <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                                            @else
                                                <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $i]) }}' + '')">{{ $i }}</or>
                                            @endif
                                        @endfor
                                    @endif
                        
                                    @if ($params['page'] < $params['pages'])
                                        <or class="gold clickable" style="padding-left:2px" onclick="navPage('{{ route('changelog', ['start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'table' => $params['table'], 'user' => $params['user'],'page' => $params['page']-1]) }}' + '')">></or>
                                    @endif
                                @else 
                                    <form action="{{ route('changelog.filter') }}" method="POST" style="margin-bottom:0px">
                                        @csrf
                                        <table class="centertable">
                                            <tbody>
                                                <tr>
                                                    <input type="hidden" name="start-date" value="{{ $params['start_date'] }}"></input>
                                                    <input type="hidden" name="end-date" value="{{ $params['end_date'] }}"></input>
                                                    <input type="hidden" name="table" value="{{ $params['table'] }}"></input>
                                                    <input type="hidden" name="userid" value="{{ $params['user'] }}"></input>
                                                    <td style="padding-right:10px">Page:</td>
                                                    <td style="padding-right:10px">
                                                        <select id="page-select" class="form-control row-dropdown" style="width:60px;height:25px; padding:0px 0px 0px 10px" onchange="this.form.submit()" name="page">
                                                        @for ($i = 1; $i <= $params['pages']; $i++) 
                                                            <option value="{{ $i }}" @if ($i == $params['page']) selected @endif>{{ $i }}</option>
                                                        @endfor
                                                        </select>
                                                    </td>
                                                <tr>
                                            </tbody>
                                        </table>        
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
    <script src="{{ asset('js/changelog.js') }}"></script>
</html>
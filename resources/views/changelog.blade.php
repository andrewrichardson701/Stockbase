<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
                            <form action="" method="GET" class="text-center centertable" style="max-width:max-content">
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
                                                    <option value="all">All</option>
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
                                                    <option value="all">All</option>
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
                            // Table
                            {{ dd($changelog) }}
                            <p class="container" style="margin-top:40px">Entry count: <or class="green"><?php echo($row_count); ?></or></p>
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
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
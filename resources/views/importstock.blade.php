<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>Stock Import</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="min-h-screen-sub20">
        <!-- Page Heading -->
        <header class="theme-divBg shadow" style="padding-top:60px; margin-bottom:20px">
            <div class="nav-row-alt max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 ">
                <h2 class="font-semibold text-xl  leading-tight nav-div-alt headerfix">
                    Stock Import
                </h2>
            </div>
        </header>
        @include('includes.response-handling')

        <div class="container"  style="padding-top:25px">
            <form action="{{ route('stock.add.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="stockFile">Upload Stock File</label>
                    <input type="file" class="form-control-file" id="import_file" name="import_file" accept=".csv">
                </div>
                <button type="submit" class="btn btn-primary">Import</button>
            </form>
        </div>

    </div>

    @include('foot')
</body>

<!-- Move Stock -->
<div class="container well-nopad theme-divBg">
    <input id="hidden-page-number" type="hidden" value="{{ $params['page'] }}'" />
    <pre id="hidden-sql" hidden></pre>
@if($params['stock_id'] == 0)
    <!-- /stock/0/move -->
    <!-- search for stock -->
    @include('includes.stock.move.search')
@else
    <!-- /stock/#/move -->
    <!-- move quantity of selected stock -->

    <!-- MISSING -->
@endif
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/stock-move.js') }}"></script>
</div>



<div class="container well-nopad theme-divBg viewport-large-empty" style="margin-top:5px">
    @if (isset($transactions) && $transactions['count'] > 0)
    <h2 style="font-size:22px">Transactions</h2>
    @include('includes.stock.transactions')
    @endif
</div>
<div class="container well-nopad theme-divBg viewport-small-empty text-center" style="margin-top:5px">
    <or class="specialColor clickable" style="font-size:12px" onclick="navPage('{{ url('transactions') }}/{{ $params['stock_id'] }}')">View Transactions</or>
</div>
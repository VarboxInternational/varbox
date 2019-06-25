<h1 class="page-title float-left">@yield('title', '')</h1>

@if(isset($items) && $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    @php($total = number_format($items->total(), 0, ',', '.'))
    @php($start = $items->currentPage() * $items->perPage() - ($items->perPage() - 1))
    @if($items->currentPage() * $items->perPage() < $items->total())
        @php($end = $items->currentPage() * $items->perPage())
    @else
        @php($end = $items->total())
    @endif

    <div class="page-subtitle float-left">
        {{ $start }} - {{ $end }} of {{ $total }}
    </div>
@endif
@if(isset($items))
    @if($items instanceof \Illuminate\Support\Collection)
        @php($total = number_format($items->count(), 0, ',', '.'))

        @if($total > 0)
            <div class="page-subtitle float-right">
                1 - {{ $total }} of {{ $total }}
            </div>
        @endif
    @endif
    @if($items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        @php($total = number_format($items->total(), 0, ',', '.'))

        @if($total > 0)
            @php($start = $items->currentPage() * $items->perPage() - ($items->perPage() - 1))
            @if($items->currentPage() * $items->perPage() < $items->total())
                @php($end = $items->currentPage() * $items->perPage())
            @else
                @php($end = $items->total())
            @endif

            <div class="page-subtitle float-right">
                {{ $start }} - {{ $end }} of {{ $total }}
            </div>
        @endif
    @endif
@endif

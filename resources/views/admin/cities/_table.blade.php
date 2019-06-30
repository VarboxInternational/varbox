<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->name ?: 'N/A' }}</div>

                    @if($item->country && $item->country->exists)
                        <div class="small text-muted">
                            {{ ($item->country->name ?: 'N/A') . ($item->state && $item->state->exists && !empty($item->state->name) ? ', ' . $item->state->name : '') }}
                        </div>
                    @endif
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->editRecord(route('admin.cities.edit', $item)) !!}
                    {!! button()->deleteRecord(route('admin.cities.destroy', $item)) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="address">
                <i class="fa fa-sort mr-2"></i>Address
            </th>
            <th class="d-none d-sm-table-cell">Location</th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    {{{ Str::limit(strip_tags($item->address ?? 'N/A'), 30) }}}
                </td>
                <td class="d-none d-sm-table-cell">
                    <div>{{ optional($item->city)->name ?: 'N/A' }}</div>
                    <div class="small text-muted">
                        {{ optional($item->country)->name ?: 'N/A' }}, {{ optional($item->state)->name ?: 'N/A' }}
                    </div>
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->editRecord(route('admin.addresses.edit', [$user->getKey(), $item->getKey()])) !!}
                    {!! button()->deleteRecord(route('admin.addresses.destroy', [$user->getKey(), $item->getKey()])) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="drafted_at">
                <i class="fa fa-sort mr-2"></i>Published
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="deleted_at">
                <i class="fa fa-sort mr-2"></i>Trashed
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->name ?: 'N/A' }}</div>
                    @if($item->type)
                        <div class="small text-muted">{{ $item->type }}</div>
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->isDrafted()) badge-danger @else badge-success @endif">
                        {{ $item->isDrafted() ? 'No' : 'Yes' }}
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->trashed()) badge-danger @else badge-success @endif">
                        {{ $item->trashed() ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @if($item->trashed())
                        {!! button()->restoreRecord(route('admin.emails.restore', $item->getKey())) !!}
                        {!! button()->deleteRecord(route('admin.emails.delete', $item->getKey())) !!}
                    @else
                        {!! button()->editRecord(route('admin.emails.edit', $item->getKey())) !!}
                        {!! button()->deleteRecord(route('admin.emails.destroy', $item->getKey())) !!}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
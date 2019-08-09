<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="code">
                <i class="fa fa-sort mr-2"></i>Code
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->name ?: 'N/A' }}</div>
                    <div class="small text-muted">{{ optional($item->country)->name ?: 'N/A' }}</div>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-default">
                        {{ $item->code ?: 'N/A' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('states-edit')
                        {!! button()->editRecord(route('admin.states.edit', $item)) !!}
                    @endpermission
                    @permission('states-delete')
                        {!! button()->deleteRecord(route('admin.states.destroy', $item)) !!}
                    @endpermission
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>

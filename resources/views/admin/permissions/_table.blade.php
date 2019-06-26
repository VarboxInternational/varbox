<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="guard">
                <i class="fa fa-sort mr-2"></i>Guard
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>{{ $item->name ?: 'N/A' }}</td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-default">
                        {{ $item->guard ?: 'N/A' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->editRecord(route('admin.permissions.edit', $item)) !!}
                    {!! button()->deleteRecord(route('admin.permissions.destroy', $item)) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
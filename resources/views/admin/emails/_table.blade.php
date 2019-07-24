<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="type">
                <i class="fa fa-sort mr-2"></i>Type
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
                    {{ $item->name ?: 'N/A' }}
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $item->type ?: 'N/A' }}
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-success">
                        Yes
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-success">
                        No
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->editRecord(route('admin.emails.edit', $item->getKey())) !!}
                    {!! button()->deleteRecord(route('admin.emails.destroy', $item->getKey())) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
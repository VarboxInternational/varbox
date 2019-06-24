<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="email">
                <i class="fa fa-sort mr-2"></i>Email
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="first_name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="active">
                <i class="fa fa-sort mr-2"></i>Active
            </th>
            <th class="text-right"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>{{ $item->email ?: 'N/A' }}</td>
                <td class="d-none d-sm-table-cell">
                    {{ $item->full_name ?: 'N/A' }}
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->active) badge-success @else badge-danger @endif">
                        @if($item->active) Yes @else No @endif
                    </span>
                </td>
                <td class="text-right">
                    {!! button()->editRecord(route('admin.admins.edit', $item->getKey())) !!}
                    {!! button()->deleteRecord(route('admin.admins.destroy', $item->getKey())) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
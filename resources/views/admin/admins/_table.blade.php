<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="email">
                <i class="fa fa-sort mr-2"></i>Admin
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="active">
                <i class="fa fa-sort mr-2"></i>Status
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="created_at">
                <i class="fa fa-sort mr-2"></i>Joined At
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->email ?: 'N/A' }}</div>
                    <div class="small text-muted">{{ $item->name ?: 'N/A' }}</div>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->active) badge-success @else badge-danger @endif">
                        @if($item->active) active @else inactive @endif
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <div>{{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</div>
                    <div class="small text-muted">{{ $item->created_at ? $item->created_at->diffForHumans() : 'N/A' }}</div>
                </td>
                <td class="text-right d-table-cell">
                    @permission('admins-edit')
                        @include('varbox::buttons.edit', ['url' => route('admin.admins.edit', $item->getKey())])
                    @endpermission
                    @permission('admins-delete')
                    @include('varbox::buttons.delete', ['url' => route('admin.admins.destroy', $item->getKey())])
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

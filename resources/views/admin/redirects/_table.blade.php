<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="old_url">
                <i class="fa fa-sort mr-2"></i>Old URL
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="new_url">
                <i class="fa fa-sort mr-2"></i>New URL
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="status">
                <i class="fa fa-sort mr-2"></i>Status
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    {{ $item->old_url ?: 'N/A' }}
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $item->new_url ?: 'N/A' }}
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-default" style="font-size: 90%;">
                        {{ $item->status ?: 'N/A' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('redirects-edit')
                        @include('varbox::buttons.edit', ['url' => route('admin.redirects.edit', $item->getKey())])
                    @endpermission
                    @permission('redirects-delete')
                        @include('varbox::buttons.delete', ['url' => route('admin.redirects.destroy', $item->getKey())])
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

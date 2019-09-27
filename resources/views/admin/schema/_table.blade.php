<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="guard">
                <i class="fa fa-sort mr-2"></i>Type
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->name ?: 'N/A' }}</div>
                    <div class="small text-muted">For {{ $targets[$item->target] ?? 'N/A' }}</div>
                </td>
                <td class="d-none d-sm-table-cell text-muted">
                    <span class="badge badge badge-default" style="font-size: 90%;">
                        {{ $types[$item->type] ?? 'N/A' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('schema-edit')
                        @include('varbox::buttons.edit', ['url' => route('admin.schema.edit', $item->getKey())])
                    @endpermission
                    @permission('schema-delete')
                        @include('varbox::buttons.delete', ['url' => route('admin.schema.destroy', $item->getKey())])
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

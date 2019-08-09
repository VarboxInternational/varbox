<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="key">
                <i class="fa fa-sort mr-2"></i>Key
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="value">
                <i class="fa fa-sort mr-2"></i>Value
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>{{ $keys[$item->key] ?? 'N/A' }}</td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-default" style="font-size: 90%;">
                        {{ $item->getAttributes()['value'] ?? 'N/A' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('configs-edit')
                        {!! button()->editRecord(route('admin.configs.edit', $item)) !!}
                    @endpermission
                    @permission('configs-delete')
                        {!! button()->deleteRecord(route('admin.configs.destroy', $item)) !!}
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

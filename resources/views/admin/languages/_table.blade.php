<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="default">
                <i class="fa fa-sort mr-2"></i>Default
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="active">
                <i class="fa fa-sort mr-2"></i>Status
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->name ?: 'N/A' }}</div>
                    @if($item->code)
                        <div class="small text-muted">{{ strtoupper($item->code) }}</div>
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->default) badge-info @else badge-default @endif">
                        @if($item->default) yes @else no @endif
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->active) badge-success @else badge-danger @endif">
                        @if($item->active) active @else inactive @endif
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('languages-edit')
                        @include('varbox::buttons.edit', ['url' => route('admin.languages.edit', $item->getKey())])
                    @endpermission
                    @permission('languages-delete')
                        @include('varbox::buttons.delete', ['url' => route('admin.languages.destroy', $item->getKey())])
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

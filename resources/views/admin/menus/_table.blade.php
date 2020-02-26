<div class="card">
    <table class="table card-table table-vcenter js-TreeTable">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="type">
                <i class="fa fa-sort mr-2"></i>Type
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
                    <a @if($item->url) href="{{ $item->url ?: '#' }}" target="_blank" @endif>
                        {{ $item->uri ?: 'N/A' }}
                    </a>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge-default" style="font-size: 85%">
                        {{ $types[$item->type] ?? 'N/A' }}
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->active) badge-success @else badge-danger @endif">
                        {{ $item->active ? 'active' : 'inactive' }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('menus-edit')
                        @include('varbox::buttons.edit', ['url' => route('admin.menus.edit', ['location' => $location, 'menu' => $item->getKey()])])
                    @endpermission
                    @permission('menus-delete')
                        @include('varbox::buttons.delete', ['url' => route('admin.menus.destroy', ['location' => $location, 'menu' => $item->getKey()])])
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

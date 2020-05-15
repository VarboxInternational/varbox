<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="type">
                <i class="fa fa-sort mr-2"></i>Error
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="url">
                <i class="fa fa-sort mr-2"></i>Url
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="occurrences">
                <i class="fa fa-sort mr-2"></i>Occ
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="updated_at">
                <i class="fa fa-sort mr-2"></i>Last At
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>
                        <span @if($item->type) data-toggle="tooltip" data-placement="top" title="{{ $item->type }}" @endif>
                            {{ $item->type ? Arr::last(explode('\\', $item->type)) : 'N/A' }}
                        </span>
                    </div>
                    <div class="text-muted">Code: {{ $item->code ?? 'N/A' }}</div>
                </td>
                <td class="d-none d-sm-table-cell text-gray">
                    @if($item->url)
                        @include('varbox::buttons.link', ['url' => url($item->url)])
                    @else
                        N/A
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-default" style="font-size: 90%;">
                        {{ $item->occurrences ?: 0 }}
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <div>{{ $item->updated_at }}</div>
                    <div class="text-muted">{{ $item->updated_at->diffForHumans()}}</div>
                </td>
                <td class="text-right d-table-cell">
                    @permission('errors-view')
                        @include('varbox::buttons.view', ['url' => route('admin.errors.show', $item->getKey())])
                    @endpermission
                    @permission('errors-delete')
                        @include('varbox::buttons.delete', ['url' => route('admin.errors.destroy', $item->getKey())])
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

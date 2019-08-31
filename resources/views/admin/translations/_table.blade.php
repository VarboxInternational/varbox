<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="key">
                <i class="fa fa-sort mr-2"></i>Key
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="value">
                <i class="fa fa-sort mr-2"></i>Value
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="locale">
                <i class="fa fa-sort mr-2"></i>Locale
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->key ?: 'N/A' }}</div>
                    @if($item->group)
                        <div class="small text-muted">{{ $item->group }}</div>
                    @endif
                </td>
                <td class="d-none d-sm-table-cell">
                    <span @if(!empty($item->value) && strlen($item->value) > 30) data-toggle="tooltip" data-placement="top" title="{{ $item->value }}" @endif>
                        {{ $item->value ? Str::limit(strip_tags($item->value), 30) : 'N/A' }}
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge-default">
                        {{ strtoupper($item->locale ?: 'N/A') }}
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    @permission('translations-edit')
                        {!! button()->editRecord(route('admin.translations.edit', $item->getKey())) !!}
                    @endpermission
                    @permission('translations-delete')
                        {!! button()->deleteRecord(route('admin.translations.destroy', $item->getKey())) !!}
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

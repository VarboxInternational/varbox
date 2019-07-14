<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="size">
                <i class="fa fa-sort mr-2"></i>Size
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="created_at">
                <i class="fa fa-sort mr-2"></i>Created at
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>{!! $item->name ?: 'N/A' !!}</td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge badge badge-default" style="font-size: 90%;">
                        {{ $item->size_in_mb ?: 0 }} MB
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <div>{{ $item->date }}</div>
                    <div class="text-muted">{{ Carbon\Carbon::parse($item->date)->diffForHumans()}}</div>
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->downloadFile(route('admin.backups.download', $item->getKey())) !!}
                    {!! button()->deleteRecord(route('admin.backups.destroy', $item->getKey())) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th>
                Activity
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="created_at">
                <i class="fa fa-sort mr-2"></i>Logged at
            </th>
            <th class="text-right"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>{!! $item->message ?: 'N/A' !!}</td>
                <td class="d-none d-sm-table-cell">
                    {{ $item->created_at ?: 'N/A' }}
                </td>
                <td class="text-right">
                    {!! button()->deleteRecord(route('admin.activity.destroy', $item->getKey())) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
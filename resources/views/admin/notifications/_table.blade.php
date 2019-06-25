<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th>Subject</th>
            <th class="sortable d-none d-sm-table-cell" data-sort="read_at">
                <i class="fa fa-sort mr-2"></i>Read
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="created_at">
                <i class="fa fa-sort mr-2"></i>Received At
            </th>
            @if(!$isAnotherUser)
                <th class="text-right d-flex justify-content-end"></th>
            @endif
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td >
                    <span @if(!empty($item->data['subject'])) data-toggle="tooltip" data-placement="right" title="{{ $item->data['subject'] }}" @endif>
                        {{ Str::limit($item->data['subject'] ?? 'N/A', 30) }}
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                     <span class="badge badge badge-{!! $item->read() ? 'success' : 'danger' !!}">
                        {{ $item->read() ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    @if($item->created_at)
                        <div>{{ $item->created_at }}</div>
                        <div class="text-muted">{{ Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</div>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>
                @if(!$isAnotherUser)
                    <td class="text-right d-flex justify-content-end">
                        <a href="{{ route('admin.notifications.action', $item->id) }}" class="btn icon d-inline bg-white px-0 mr-2" data-toggle="tooltip" data-placement="top" title="Action">
                            <i class="fe fe-check-square text-blue"></i>
                        </a>

                        {!! form()->open(['url' => route('admin.notifications.mark_as_read', $item->id), 'method' => 'PUT', 'class' => 'd-inline']) !!}
                        {!! form()->button('<i class="fe fe-eye text-green"></i>', ['type' => 'submit', 'class' => 'btn icon d-inline bg-white px-0', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Mark As Read']) !!}
                        {!! form()->close() !!}

                        {!! button()->deleteRecord(route('admin.notifications.destroy', $item->getKey())) !!}
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
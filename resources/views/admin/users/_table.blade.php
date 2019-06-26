<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="email">
                <i class="fa fa-sort mr-2"></i>User
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="active">
                <i class="fa fa-sort mr-2"></i>Status
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="created_at">
                <i class="fa fa-sort mr-2"></i>Joined At
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <div>{{ $item->email ?: 'N/A' }}</div>
                    <div class="small text-muted">{{ $item->full_name ?: 'N/A' }}</div>
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->active) badge-success @else badge-danger @endif">
                        @if($item->active) active @else inactive @endif
                    </span>
                </td>
                <td class="d-none d-sm-table-cell">
                    <div>{{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</div>
                    <div class="small text-muted">{{ $item->created_at ? $item->created_at->diffForHumans() : 'N/A' }}</div>
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->editRecord(route('admin.users.edit', $item->getKey())) !!}
                    {!! button()->deleteRecord(route('admin.users.destroy', $item->getKey())) !!}

                    <div class="item-action dropdown" data-toggle="tooltip" data-placement="top" title="More">
                        <a href="javascript:void(0)" data-toggle="dropdown" class="d-inline btn icon px-0" aria-expanded="false" >
                            <i class="fe fe-more-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(15px, 20px, 0px); top: 0px; left: 0px; will-change: transform;">
                            {!! form()->open(['url' => route('admin.users.impersonate', $item->getKey()), 'method' => 'post', 'target' => '_blank']) !!}
                            {!! form()->button('<i class="dropdown-icon fe fe-user mr-2"></i>Impersonate', ['type' => 'submit', 'class' => 'dropdown-item', 'style' => 'cursor: pointer;']) !!}
                            {!! form()->close() !!}
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>
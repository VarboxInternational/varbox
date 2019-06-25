<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="email">
                <i class="fa fa-sort mr-2"></i>Email
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="first_name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-sm-table-cell" data-sort="active">
                <i class="fa fa-sort mr-2"></i>Active
            </th>
            <th class="text-right"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>{{ $item->email ?: 'N/A' }}</td>
                <td class="d-none d-sm-table-cell">
                    {{ $item->full_name ?: 'N/A' }}
                </td>
                <td class="d-none d-sm-table-cell">
                    <span class="badge @if($item->active) badge-success @else badge-danger @endif">
                        @if($item->active) Yes @else No @endif
                    </span>
                </td>
                <td class="text-right">
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
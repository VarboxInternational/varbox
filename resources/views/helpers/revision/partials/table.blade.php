<table class="table card-table table-vcenter">
    <tr>
        <th class="d-none d-sm-table-cell w-1 text-center">#</th>
        <th>Created By</th>
        <th>Created At</th>
        <th class="text-right d-table-cell"></th>
    </tr>
    @forelse($revisions as $index => $revision)
        <tr>
            <td class="d-none d-sm-table-cell w-1">
                <span class="badge badge badge-default" style="font-size: 95%;">
                    {{ $index + 1 }}
                </span>
            </td>
            <td>
                @if($revision->user && $revision->user->exists)
                    <a href="{{ $revision->user->isAdmin() ? route('admin.admins.edit', $revision->user->getKey()) : route('admin.users.edit', $revision->user->getKey()) }}" target="_blank">
                @endif
                    {{ optional($revision->user)->email ?: 'No User' }}
                @if($revision->user && $revision->user->exists)
                    </a>
                @endif
            </td>
            <td>
                <div>{{ $revision->created_at ?: 'N/A' }}</div>
                <div class="small text-muted">{{ optional($revision->created_at)->diffForHumans() ?: 'N/A' }}</div>
            </td>
            <td class="text-right d-table-cell">
                <a href="{{ route($route, (array)$parameters + ['revision' => $revision->getKey()]) }}" class="button-view-revision d-inline btn icon px-0 mr-4" data-toggle="tooltip" data-placement="top" title="View">
                    <i class="fe fe-eye text-yellow"></i>
                </a>
                @permission('revisions-rollback')
                <a href="{{ route('admin.revisions.rollback', $revision->getKey()) }}" class="button-rollback-revision d-inline btn icon px-0 mr-4" data-toggle="tooltip" data-placement="top" title="Rollback">
                    <i class="fe fe-refresh-ccw text-blue"></i>
                </a>
                @endpermission
                @permission('revisions-delete')
                    <a href="{{ route('admin.revisions.destroy', $revision->getKey()) }}" class="button-delete-revision d-inline btn icon px-0" data-toggle="tooltip" data-placement="top" title="Delete">
                        <i class="fe fe-trash text-red"></i>
                    </a>
                @endpermission
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10">
                There are no revisions for this record
            </td>
        </tr>
    @endforelse
</table>

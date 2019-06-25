<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td>Subject</td>
        <td>Created at</td>
        <td>Read</td>
        <td class="actions-notifications">Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->data['subject'] ?? 'N/A' }}</td>
                <td>{{ $item->created_at ?: 'N/A' }}</td>
                <td>
                        <span class="flag {!! $item->read_at === null ? 'red' : 'green' !!}">
                            {{ $item->read_at === null ? 'No' : 'Yes' }}
                        </span>
                </td>
                <td>
                    {!! button()->action('Action', route('admin.notifications.action', $item->id), 'fa-check', 'blue no-margin-bottom no-margin-top no-margin-left', ['target' => '_blank']) !!}
                    {!! form()->open(['url' => route('admin.notifications.mark_as_read', $item->id), 'method' => 'PUT']) !!}
                    {!! form()->button('<i class="fa fa-check"></i>&nbsp; Mark as read', ['type' => 'submit', 'class' => 'btn green no-margin-bottom no-margin-top']) !!}
                    {!! form()->close() !!}
                    {!! button()->deleteRecord(route('admin.notifications.destroy', $item->id)) !!}
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="10">No records found</td>
        </tr>
    @endif
    </tbody>
</table>

<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="sortable" data-sort="address">
                <i class="fa fa-sort mr-2"></i>Address
            </th>
            <th class="d-none d-sm-table-cell">Location</th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($user->addresses as $index => $item)
            @php($address = [])
            @if($item->city && $item->city->exists)
                @php($address[] = $item->city->name)
            @endif
            @if($item->state && $item->state->exists)
                @php($address[] = $item->state->name)
            @endif
            @if($item->country && $item->country->exists)
                @php($address[] = $item->country->name)
            @endif
            <tr>
                <td>
                    {{{ Str::limit(strip_tags($item->address ?? 'N/A'), 40) }}}
                </td>
                <td class="d-none d-sm-table-cell">
                    @if(count($address) > 0)
                        <div>{{ $address[0] }}</div>
                        @if(count($address) > 1)
                            <div class="small text-muted">
                                {{ $address[1] }}{{ isset($address[2]) ? ', ' . $address[2] : '' }}
                            </div>
                        @endif
                    @else
                        N/A
                    @endif
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->editRecord(route('admin.addresses.edit', [$user->getKey(), $item->getKey()])) !!}
                    {!! button()->deleteRecord(route('admin.addresses.destroy', [$user->getKey(), $item->getKey()])) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>

<div class="card">
    <table class="table card-table table-vcenter">
        <tr>
            <th class="w-1"></th>
            <th class="sortable" data-sort="original_name">
                <i class="fa fa-sort mr-2"></i>Name
            </th>
            <th class="sortable d-none d-md-table-cell" data-sort="size">
                <i class="fa fa-sort mr-2"></i>Size
            </th>
            <th class="text-right d-table-cell"></th>
        </tr>
        @forelse($items as $index => $item)
            <tr>
                <td>
                    <a href="{{ uploaded($item->full_path)->url() }}" target="_blank">
                        <span class="avatar d-block rounded bg-white" style="background-image: url({{ uploaded($item->full_path)->thumbnail() }})">
                            @if($item->isAudio())
                                <i class="fa fa-file-audio text-blue" style="vertical-align: middle; font-size: 245%;"></i>
                            @elseif($item->isFile())
                                <i class="fa fa-file-alt text-blue" style="vertical-align: middle; font-size: 245%;"></i>
                            @endif
                        </span>
                    </a>
                </td>
                <td>
                    <div class="upload-name text-truncate">{{ $item->original_name ?: 'N/A' }}</div>
                    <div class="small text-muted">{{ $item->mime ?: 'N/A' }}</div>
                </td>
                <td class="d-none d-md-table-cell">
                    <span class="badge badge badge-default" style="font-size: 90%;">
                        {{ $item->size_mb ?: 0 }} MB
                    </span>
                </td>
                <td class="text-right d-table-cell">
                    {!! button()->downloadFile(route('admin.uploads.download', $item->getKey())) !!}
                    {!! button()->viewRecord(uploaded($item->full_path)->url(), ['target' => '_blank']) !!}
                    {!! button()->deleteRecord(route('admin.uploads.destroy', $item->getKey())) !!}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endforelse
    </table>
</div>

@push('styles')
    <style>
        @media (max-width: 768px) {
            .upload-name {
                max-width: 85px;
            }
        }
    </style>
@endpush
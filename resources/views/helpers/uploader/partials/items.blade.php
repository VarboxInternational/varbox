@if(isset($uploads) && $uploads->count())
    @foreach($uploads as $upload)
        <a class="tooltip" title="{!! $upload->original_name !!}" data-path="{{ $upload->full_path }}">
            @if($upload->isImage())
                <img src="{{ $upload->helper->thumbnail() }}" />
            @elseif($upload->isVideo())
                <img src="{{ $upload->helper->thumbnail(1) }}" />
            @else
                <img src="{{ $upload->type_icon }}" />
            @endif
        </a>
    @endforeach
@else
    <p>No {{ str_plural($type) }} found</p>
@endif
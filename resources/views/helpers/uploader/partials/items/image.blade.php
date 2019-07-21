@if(isset($uploads) && $uploads->count())
    @foreach($uploads as $upload)
        <a class="btn-upload-select m-2 d-block float-left" data-toggle="tooltip" data-placement="top" title="{!! $upload->original_name !!}" data-path="{{ $upload->full_path }}" style="cursor: pointer;">
            <img src="{{ $upload->helper->thumbnail() }}" width="90" height="90" />
        </a>
    @endforeach
@else
    <p class="px-2 text-muted-dark">No images found</p>
@endif
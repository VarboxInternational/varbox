<div class="form-group">
    <label class="form-label">
        {!! $label !!}
    </label>
    <div class="d-flex w-100 ">
        @include('varbox::helpers.uploader.partials.new')
        @include('varbox::helpers.uploader.partials.current')
    </div>
    {!! form()->hidden($field, $current ? $current->path('original') : null, ['class' => 'js-UploadInput js-UploadInput-' . $index]) !!}
</div>

@if($i == 0)
    @include('varbox::helpers.uploader.partials.scripts')
@endif
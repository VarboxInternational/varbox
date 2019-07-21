<div class="form-group">
    <label class="form-label">{!! $label !!}<span class="form-required">*</span></label>

    <div class="d-flex w-100 ">
        @include('varbox::helpers.uploader.partials.new')
        @include('varbox::helpers.uploader.partials.current')
    </div>

    {!! form()->hidden($field, $current ? $current->path('original') : null, ['id' => 'upload-input-' . $index, 'class' => 'upload-input']) !!}
</div>

@if($i == 0)
    @include('varbox::helpers.uploader.partials.scripts')
@endif
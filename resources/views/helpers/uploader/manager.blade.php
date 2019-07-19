<fieldset>
    <label>{!! $label !!}</label>
    <div class="field-wrapper">
        @include('varbox::helpers.uploader.partials.new')
        @include('varbox::helpers.uploader.partials.current')
    </div>
    {!! form()->hidden($field, $current ? $current->path('original') : null, ['id' => 'upload-input-' . $index, 'class' => 'upload-input']) !!}
</fieldset>

@if($i == 0)
    @include('varbox::helpers.uploader.partials.scripts')
@endif

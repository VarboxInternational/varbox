@if($locations && is_array($locations) && !empty($locations))
    @foreach($locations as $location)
        <span class="block-location-title">{{ title_case(str_replace(['_', '-'], ' ', $location)) }}</span>

        <div class="blocks-location-container" data-location="{{ $location }}">
            <table class="blocks-table" cellspacing="0" cellpadding="0" border="0">
                @include('varbox::helpers.block.partials.table', ['location' => $location])
            </table>

            @if($disabled === false)
                @permission('blocks-assign')
                    <div class="block-assign-container">
                        <div class="block-assign-select-container">
                            <select class="block-assign-select">
                                <option value="" selected="selected"></option>
                                @foreach($model->getBlocksOfLocation($location) as $block)
                                    <option value="{{ $block->id }}">{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="block-assign-btn-container">
                            <a href="#" class="block-assign btn green no-margin right">
                                <i class="fa fa-plus"></i>&nbsp; Assign
                            </a>
                        </div>
                    </div>
                @endpermission
            @endif
        </div>
    @endforeach
@endif

<div class="blocks-request">
    @if($blocks->count() > 0)
        @foreach($blocks as $index => $block)
            @php $pivot = $block->pivot; @endphp
            {!! form()->hidden('blocks[' . $pivot->id . '][' . $block->id . ']', $pivot->block_id, ['class' => 'block-input', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('blocks[' . $pivot->id . '][' . $block->id . '][location]', $pivot->location, ['class' => 'block-input', 'data-index' => $pivot->id]) !!}
            {!! form()->hidden('blocks[' . $pivot->id . '][' . $block->id . '][ord]', $pivot->ord, ['class' => 'block-input', 'data-index' => $pivot->id]) !!}
        @endforeach
    @endif
</div>

@php
    if (isset($model) && @array_key_exists(\Varbox\Base\Traits\IsCacheable::class, class_uses($model))) {
        $model->clearQueryCache();
    }
@endphp

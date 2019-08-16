@if($locations && is_array($locations) && !empty($locations))
    @foreach($locations as $location)
        <h4 class="px-5 text-blue @if(!$loop->first) mt-7 @endif">
            {{ Str::title(str_replace(['_', '-'], ' ', $location)) }}
            <span class="font-weight-light pl-1">Location</span>
        </h4>
        <hr class="border-primary my-2">
        <div class="js-BlocksLocationContainer" data-location="{{ $location }}">
            <table class="js-BlocksTable table card-table table-vcenter">
                @include('varbox::helpers.block.partials.table', ['location' => $location])
            </table>

            @if(!$disabled)
                @permission('blocks-assign')
                    <div class="d-flex p-5">
                        <div class="input-group">
                            <div class="flex-fill">
                                <select class="select-input" data-placeholder="Please select a block to add">
                                    <option value="" selected="selected"></option>
                                    @foreach($model->getBlocksOfLocation($location) as $block)
                                        <option value="{{ $block->id }}">{{ $block->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group-append">
                                <a href="#" class="button-assign-block btn btn-blue">
                                    <i class="fe fe-plus mr-2"></i>Add
                                </a>
                            </div>
                        </div>
                    </div>
                @endpermission
            @endif
        </div>
    @endforeach
@endif

<div class="js-BlocksRequest">
    @if($blocks->count() > 0)
        @foreach($blocks as $index => $block)
            {!! form()->hidden('blocks[' . $block->pivot->id . '][' . $block->id . ']', $block->pivot->block_id, ['data-index' => $block->pivot->id]) !!}
            {!! form()->hidden('blocks[' . $block->pivot->id . '][' . $block->id . '][location]', $block->pivot->location, ['data-index' => $block->pivot->id]) !!}
            {!! form()->hidden('blocks[' . $block->pivot->id . '][' . $block->id . '][ord]', $block->pivot->ord, ['data-index' => $block->pivot->id]) !!}
        @endforeach
    @endif
</div>

@if(in_array(\Varbox\Traits\IsCacheable::class, class_uses($model)))
    @php($model->clearQueryCache())
@endif

<thead>
    <tr class="even nodrag nodrop">
        <td>Name</td>
        <td>Type</td>
        <td class="actions-blocks">Actions</td>
    </tr>
</thead>
<tbody>
    @php($blocksInLocation = $model->getBlocksInLocation($location))
    @php($shouldInheritBlocks = (bool)$blocksInLocation->count() == 0)
    @php($inheritedBlocks = $shouldInheritBlocks ? $model->getInheritedBlocks($location) : null)
    @if($blocksInLocation->count())
        @foreach($blocksInLocation as $block)
            <tr id="{{ $block->pivot->id }}" data-block-id="{{ $block->id }}" data-index="{{ $block->pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                <td>{{ $block->name ?: 'N/A' }}</td>
                <td>{{ $block->type ?: 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.blocks.edit', $block->id) }}" class="btn yellow no-margin-top no-margin-bottom no-margin-left {!! !(auth()->user()->isSuper() || auth()->user()->hasPermission('blocks-edit')) ? 'disabled' : '' !!}" target="_blank">
                        <i class="fa fa-eye"></i>&nbsp; View
                    </a>
                    <a href="#" class="block-unassign btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true || !(auth()->user()->isSuper() || auth()->user()->hasPermission('blocks-unassign')) ? 'disabled' : '' !!}">
                        <i class="fa fa-times"></i>&nbsp; Remove
                    </a>
                </td>
            </tr>
        @endforeach
    @else
        <tr class="no-blocks-assigned nodrag nodrop">
            <td colspan="10">
                @if($inheritedBlocks && $inheritedBlocks->count() > 0)
                    <div class="block-inheritance">
                        <span>This record inherits the following blocks: </span>
                        <em>{{ $inheritedBlocks->implode('name', ', ') }}.</em>
                        <span>Assigning blocks here, will overwrite the inherited blocks.</span>
                    </div>
                @else
                    There are no blocks assigned to this location
                @endif
            </td>
        </tr>
    @endif
</tbody>

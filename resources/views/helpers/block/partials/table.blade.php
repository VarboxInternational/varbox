<thead>
    <tr class="nodrag nodrop">
        <th>Name</th>
        <th class="d-none d-sm-table-cell">Type</th>
        <th class="text-right d-table-cell"></th>
    </tr>
</thead>
<tbody>
    @php($blocksInLocation = $model->getBlocksInLocation($location))
    @php($shouldInheritBlocks = $blocksInLocation->count() == 0)
    @php($inheritedBlocks = $shouldInheritBlocks ? $model->getInheritedBlocks($location) : null)
    @forelse($blocksInLocation as $block)
        <tr id="{{ $block->pivot->id }}" data-block-id="{{ $block->id }}" data-index="{{ $block->pivot->id }}" class="border-bottom @if($disabled || !((auth()->user()->isSuper() || auth()->user()->hasPermission('blocks-order')))) nodrag nodrop @endif">
            <td>
                {{ $block->name ?: 'N/A' }}
            </td>
            <td class="d-none d-sm-table-cell">
                <span class="badge badge badge-default" style="font-size: 90%;">
                    {{ $block->type ?: 'N/A' }}
                </span>
            </td>
            <td class="text-right d-table-cell">
                @permission('blocks-edit')
                    <a href="{{ route('admin.blocks.edit', $block->getKey()) }}" class="button-view-block d-inline btn icon px-0 mr-4" target="_blank" data-toggle="tooltip" data-placement="top" title="View">
                        <i class="fe fe-eye text-yellow"></i>
                    </a>
                @endpermission
                @permission('blocks-unassign')
                    <a href="#" class="button-unassign-block d-inline btn icon px-0 {!! $disabled === true ? 'disabled' : '' !!}" data-toggle="tooltip" data-placement="top" title="Remove">
                        <i class="fe fe-x text-red"></i>
                    </a>
                @endpermission
            </td>
        </tr>
    @empty
        <tr class="js-BlocksEmpty nodrag nodrop">
            <td colspan="10">
                @if($inheritedBlocks && $inheritedBlocks->count() > 0)
                    <p class="text-gray mb-0">
                        This record inherits the following blocks: <strong>Example Block, Another bock</strong><br />
                        Please note that assigning blocks here, will overwrite the inherited blocks.
                    </p>
                @else
                    <p class="text-gray mb-0">There are no blocks assigned to this location.</p>
                @endif
            </td>
        </tr>
    @endforelse
</tbody>

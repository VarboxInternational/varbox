@permission('blocks-show')
    <div id="tab-blocks" class="tab">
        <div class="loading loading-blocks">
            <img src="{{ asset('vendor/varbox/images/loading.gif') }}" />
        </div>
        <div
            class="blocks-container"
            data-blockable-id="{{ $model->id }}"
            data-blockable-type="{{ get_class($model) }}"
            data-draft="{{ $draft ? $draft->id : null }}"
            data-revision="{{ $revision ? $revision->id : null }}"
            data-disabled="{{ $disabled }}"
        ></div>
    </div>
    <script type="x-template" id="block-row-template">
        <tr id="#index#" data-block-id="#block_id#" data-index="#index#" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
            <td>#block_name#</td>
            <td>#block_type#</td>
            <td>
                <a href="#block_url#" class="btn yellow no-margin-top no-margin-bottom no-margin-left" target="_blank">
                    <i class="fa fa-eye"></i>&nbsp; View
                </a>
                <a href="#" class="block-unassign btn red no-margin-top no-margin-bottom no-margin-right {!! $disabled === true ? 'disabled' : '' !!}">
                    <i class="fa fa-times"></i>&nbsp; Remove
                </a>
            </td>
        </tr>
    </script>
    <script type="x-template" id="no-block-row-template">
        <tr class="no-blocks-assigned nodrag nodrop">
            <td colspan="10">
                There are no blocks assigned to this location
            </td>
        </tr>
    </script>
    <script type="x-template" id="block-request-template">
        {!! form()->hidden('blocks[#index#][#block_id#]', '#block_id#', ['class' => 'block-input', 'data-index' => '#index#']) !!}
        {!! form()->hidden('blocks[#index#][#block_id#][location]', '#block_location#', ['class' => 'block-input', 'data-index' => '#index#']) !!}
        {!! form()->hidden('blocks[#index#][#block_id#][ord]', '#block_ord#', ['class' => 'block-input', 'data-index' => '#index#']) !!}
    </script>

    @php(DB::rollBack())
@endpermission

<script type="x-template" id="js-BlocksTableTemplate">
    <tr id="#index#" data-block-id="#block_id#" data-index="#index#" class="border-bottom @if($disabled || !((auth()->user()->isSuper() || auth()->user()->hasPermission('blocks-order')))) nodrag nodrop @endif">
        <td>
            #block_name#
        </td>
        <td class="d-none d-sm-table-cell">
                <span class="badge badge badge-default" style="font-size: 90%;">
                    #block_type#
                </span>
        </td>
        <td class="text-right d-table-cell">
            @permission('blocks-edit')
            <a href="#block_url#" class="button-view-block d-inline btn icon px-0 mr-4" target="_blank" data-toggle="tooltip" data-placement="top" title="View">
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
</script>

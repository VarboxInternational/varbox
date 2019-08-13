{!! validation('admin')->errors() !!}

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'frm row row-cards', 'files' => true]) !!}
@endif

{!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}

<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-blue"></div>
        <div class="card-header">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {!! form_admin()->text('name', 'Name', null, ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

@includeWhen($includeAdminView, 'blocks_' . ($item->exists ? $item->type : $type) . '::admin')

@if($item->exists)
    @if(isset($revision))
        {!! revision()->container($item, 'admin.blocks.revision', $revision) !!}
    @else
        {!! revision()->container($item, 'admin.blocks.revision') !!}
        {!! draft()->container($item, 'admin.blocks.publish', 'blocks-publish') !!}
    @endif
@endif

@if(!isset($revision))
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex text-left">
                    {!! button()->cancelAction(route('admin.blocks.index')) !!}
                    @if($item->exists)
                        @permission('blocks-duplicate')
                            {!! button()->duplicateRecord(route('admin.blocks.duplicate', $item->getKey())) !!}
                        @endpermission
                        @permission('blocks-draft')
                            @if(!$item->isDrafted())
                                {!! button()->saveAsDraft(route('admin.blocks.draft', $item->exists ? $item->getKey() : null)) !!}
                            @endif
                        @endpermission
                        {!! button()->saveAndStay() !!}
                    @else
                        @permission('blocks-draft')
                            {!! button()->saveAsDraft(route('admin.blocks.draft', $item->exists ? $item->getKey() : null)) !!}
                        @endpermission
                        {!! button()->saveAndNew() !!}
                        {!! button()->saveAndContinue('admin.blocks.edit') !!}
                    @endif
                    {!! button()->saveRecord() !!}
                </div>
            </div>
        </div>
    </div>
@endif

{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.block_form_request', \Varbox\Requests\BlockRequest::class), '.frm') !!}
@endpush

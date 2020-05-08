@include('varbox::validation')

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
        @include('varbox::helpers.revision.container', ['model' => $item, 'route' => 'admin.blocks.revision', 'revision' => $revision, 'parameters' => []])
    @else
        @include('varbox::helpers.revision.container', ['model' => $item, 'route' => 'admin.blocks.revision', 'revision' => null, 'parameters' => []])
        @include('varbox::helpers.draft.container', ['model' => $item, 'route' => 'admin.blocks.publish', 'permission' => 'blocks-publish'])
    @endif
@endif

@if(!isset($revision))
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex text-left">
                    @include('varbox::buttons.cancel', ['url' => route('admin.blocks.index')])
                    @if($item->exists)
                        @permission('blocks-duplicate')
                            @include('varbox::buttons.duplicate', ['url' => route('admin.blocks.duplicate', $item->getKey())])
                        @endpermission
                        @permission('blocks-draft')
                            @if(!$item->isDrafted())
                                @include('varbox::buttons.save_draft', ['url' => route('admin.blocks.draft', $item->exists ? $item->getKey() : null)])
                            @endif
                        @endpermission
                        @include('varbox::buttons.save_stay')
                    @else
                        @permission('blocks-draft')
                            @include('varbox::buttons.save_draft', ['url' => route('admin.blocks.draft', $item->exists ? $item->getKey() : null)])
                        @endpermission
                        @include('varbox::buttons.save_new')
                        @include('varbox::buttons.save_continue', ['route' => 'admin.blocks.edit'])
                    @endif
                    @include('varbox::buttons.save')
                </div>
            </div>
        </div>
    </div>
@endif

{!! form_admin()->close() !!}

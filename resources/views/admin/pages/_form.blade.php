{!! validation('admin')->errors() !!}

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'frm row row-cards', 'files' => true]) !!}
@endif

<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-blue"></div>
        <div class="card-header">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {!! form_admin()->text('name', 'Name', null, ['required', 'class' => 'js-SlugFrom']) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('slug', 'Slug', null, ['required', 'class' => 'js-SlugTo']) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->select('type', 'Type', ['' => 'Please select'] + $types, null, ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Content Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    {!! form_admin()->text('data[title]', 'Title') !!}
                </div>
                <div class="col-md-6">
                    {!! form_admin()->text('data[subtitle]', 'Subtitle') !!}
                </div>
                <div class="col-md-12">
                    {!! form_admin()->editor('data[content]', 'Content') !!}
                </div>
            </div>
        </div>
    </div>
</div>

@if($item->exists)
    @if(isset($revision))
        {!! block()->container($item, $revision) !!}
        {!! revision()->container($item, 'admin.pages.revision', $revision) !!}
    @else
        {!! block()->container($item, null) !!}
        {!! draft()->container($item, 'admin.pages.publish', 'pages-publish') !!}
        {!! revision()->container($item, 'admin.pages.revision') !!}
    @endif
@endif

@if(!isset($revision))
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex text-left">
                    {!! button()->cancelAction(route('admin.pages.index')) !!}

                    @permission('pages-preview')
                        {!! button()->previewRecord(route('admin.pages.preview', $item->getKey())) !!}
                    @endpermission

                    @if($item->exists)
                        @permission('pages-duplicate')
                            {!! button()->duplicateRecord(route('admin.pages.duplicate', $item->getKey())) !!}
                        @endpermission
                        @permission('pages-draft')
                            @if(!$item->isDrafted())
                                {!! button()->saveAsDraft(route('admin.pages.draft', $item->exists ? $item->getKey() : null)) !!}
                            @endif
                        @endpermission
                        {!! button()->saveAndStay() !!}
                    @else
                        @permission('pages-draft')
                            {!! button()->saveAsDraft(route('admin.pages.draft', $item->exists ? $item->getKey() : null)) !!}
                        @endpermission
                        {!! button()->saveAndNew() !!}
                        {!! button()->saveAndContinue('admin.pages.edit') !!}
                    @endif
                    {!! button()->saveRecord() !!}
                </div>
            </div>
        </div>
    </div>
@endif

{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.page_form_request', \Varbox\Requests\PageRequest::class), '.frm') !!}
@endpush

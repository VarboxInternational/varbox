{!! validation('admin')->errors() !!}

@if($item->exists)
    @if(isset($revision))
        {!! form_admin()->model($item, ['class' => 'frm row row-cards']) !!}
    @else
        {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'frm row row-cards', 'files' => true]) !!}
    @endif
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
                <div class="col-md-6">
                    {!! form_admin()->text('name', 'Name', null, ['required']) !!}
                </div>
                <div class="col-md-6">
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
                    {!! form_admin()->text('data[subject]', 'Subject') !!}
                </div>
                <div class="col-md-6">
                    {!! uploader()->field('data[attachment]')->label('Attachment')->model($item)->manager() !!}
                </div>
                <div class="col-md-12">
                    {!! form_admin()->editor('data[message]', 'Message') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-red"></div>
        <div class="card-header">
            <h3 class="card-title">Sender Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {!! form_admin()->text('data[from_name]', 'From Name', null, ['placeholder' => 'default is ' . $fromName]) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('data[from_email]', 'From Email', null, ['placeholder' => 'default is ' . $fromEmail]) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->text('data[reply_to]', 'Reply To', null, ['placeholder' => 'default is ' . $fromEmail]) !!}
                </div>
            </div>
        </div>
    </div>
</div>

@if($item->exists && !empty($variables))
<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-yellow"></div>
        <div class="card-header" data-toggle="card-collapse" style="cursor: pointer;">
            <h3 class="card-title">Variables Info</h3>
            <div class="card-options">
                <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-warning col-12 mb-5">
                <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
                    <i class="fe fe-alert-circle mr-2" aria-hidden="true"></i>
                </div>
                <div class="d-inline-block">
                    You can use the variables below in your email message, in order to display dynamic content.<br />
                    The syntax required for using variables is: <strong>[variable_name]</strong>
                </div>
            </div>
            <div class="row">
                @foreach($variables as $variable => $attributes)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <span class="badge badge-default" style="font-size: 90%;">
                                    {{ $attributes['name'] }}
                                </span>
                            </div>
                            <div class="card-body">
                                {{ $attributes['label'] }}
                            </div>
                            <div class="card-footer">
                                <span class="text-muted">Use in "Message" like:</span> <strong>[{{ $variable }}]</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

@if($item->exists)
    @if(isset($revision))
        {!! revision()->container('admin.emails.revision', $item, $revision) !!}
    @else
        {!! revision()->container('admin.emails.revision', $item) !!}
        {!! draft()->container('admin.emails.publish', $item) !!}
    @endif
@endif

@if(!isset($revision))
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex text-left">
                    {!! button()->cancelAction(route('admin.emails.index')) !!}
                    {!! button()->previewRecord(route('admin.emails.preview', $item->getKey())) !!}
                    @if($item->exists)
                        {!! button()->duplicateRecord(route('admin.emails.duplicate', $item->getKey())) !!}
                        @if(!$item->isDrafted())
                            {!! button()->saveAsDraft(route('admin.emails.draft', $item->getKey())) !!}
                        @endif
                        {!! button()->saveAndStay() !!}
                    @else
                        {!! button()->saveAndNew() !!}
                        {!! button()->saveAsDraft(route('admin.emails.draft')) !!}
                        {!! button()->saveAndContinue('admin.emails.edit') !!}
                    @endif
                    {!! button()->saveRecord() !!}
                </div>
            </div>
        </div>
    </div>
@endif

{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.email_form_request', \Varbox\Requests\EmailRequest::class), '.frm') !!}
@endpush

@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    {!! validation('admin')->errors() !!}

    {!! form_admin()->model($item, ['url' => route('admin.schema.update', $item->getKey()), 'method' => 'put', 'class' => 'frm row row-cards', 'files' => true]) !!}
    {!! form()->hidden('type', $item->type) !!}
    <div class="col-12">
        <div class="card">
            <div class="card-status bg-blue"></div>
            <div class="card-header">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        {!! form_admin()->text('name', 'Name', null, ['required']) !!}
                    </div>
                    <div class="col-6">
                        {!! form_admin()->select('target', 'Apply On', ['' => 'Please select'] + $targets, null, ['required']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-status bg-green"></div>
            <div class="card-header">
                <h3 class="card-title">Fields Info</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning mb-5">
                            <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
                                <i class="fe fe-alert-circle mr-2" aria-hidden="true"></i>
                            </div>
                            <div class="d-inline-block">
                                <p>
                                    Below you have all the necessary fields for a <strong>{{ $types[$item->type] }} Schema</strong>.<br />
                                    In order for the Schema to work properly, please associate the fields below with a database field.<br />
                                </p>
                                <p>To associate fields please use 1 of the 4 available methods:</p>
                                <p>
                                    <strong>1. Associate a database table column from the model.</strong><br />
                                    In this case, just simply write the column's name.<br />
                                    <em>--- Syntax:</em> column_name<br />
                                    <em>--- Example:</em> title
                                </p>
                                <p>
                                    <strong>2. Associate a multi-depth database table column of the model.</strong><br />
                                    This is useful in case you want to associate individual keys from a "json" or "text" column.<br />
                                    <em>--- Syntax:</em> column_name[key_name]<br />
                                    <em>--- Example:</em> data[title]
                                </p>
                                <p>
                                    <strong>3. Associate a database table column from a relation of the model.</strong><br />
                                    This is useful in case you want to associate values from fields related to the model.<br />
                                    Please note that only one-to-one and many-to-one relations are supported.<br />
                                    <em>--- Syntax:</em> relation_name.column.name<br />
                                    <em>--- Example:</em> author.name
                                </p>
                                <p class="mb-0">
                                    <strong>4. Associate a hard-coded value.</strong><br />
                                    In exceptional cases you may need to insert hard-coded values for certain Schema fields.<br />
                                    Please note that the hard-coded value inserted will apply for all records of the applied model.
                                </p>
                            </div>
                        </div>
                    </div>

                    @include('varbox::admin.schema.fields.' . $item->type)
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex text-left">
                    @include('varbox::buttons.cancel', ['url' => route('admin.schema.index')])
                    @include('varbox::buttons.save_stay')
                    @include('varbox::buttons.save')
                </div>
            </div>
        </div>
    </div>
    {!! form_admin()->close() !!}
@endsection

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.schema_form_request', \Varbox\Requests\SchemaRequest::class), '.frm') !!}
@endpush

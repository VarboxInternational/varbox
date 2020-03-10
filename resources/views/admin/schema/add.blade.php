@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    {!! validation('admin')->errors() !!}

    {!! form_admin()->open(['url' => route('admin.schema.store'), 'method' => 'POST', 'class' => 'frm row row-cards', 'files' => true]) !!}
    {!! form()->hidden('type', $type) !!}

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
            <div class="card-body">
                <div class="d-flex text-left">
                    @include('varbox::buttons.cancel', ['url' => route('admin.schema.index')])

                    <a class="button-save-continue btn btn-primary btn-square text-white ml-4" data-route-name="{{ 'admin.schema.edit' }}">
                        <i class="fe fe-arrow-right mr-2"></i>Continue
                    </a>
                </div>
            </div>
        </div>
    </div>
    {!! form()->close() !!}
@endsection

@push('scripts')
    {{--{!! JsValidator::formRequest(config('varbox.bindings.form_requests.schema_form_request', \Varbox\Requests\SchemaRequest::class), '.frm') !!}--}}
@endpush

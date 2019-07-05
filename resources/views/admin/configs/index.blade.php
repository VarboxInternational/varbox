@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            {!! button()->addRecord(route('admin.configs.create')) !!}

            @include('varbox::admin.configs._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.configs._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection


@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            {!! button()->addRecord(route('admin.roles.create')) !!}

            @include('varbox::admin.roles._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.roles._table')

            {!! pagination('admin')->render($items) !!}
        </div>
    </div>
@endsection

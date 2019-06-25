@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            {!! button()->addRecord(route('admin.users.create')) !!}

            @include('varbox::admin.auth.users._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.auth.users._table')

            {!! pagination('admin')->render($items) !!}
        </div>
    </div>
@endsection

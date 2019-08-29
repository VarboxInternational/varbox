@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('languages-add')
                {!! button()->addRecord(route('admin.languages.create')) !!}
            @endpermission

            @include('varbox::admin.languages._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.languages._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection

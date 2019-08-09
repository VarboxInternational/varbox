@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('roles-add')
                {!! button()->addRecord(route('admin.roles.create')) !!}
            @endpermission

            @include('varbox::admin.roles._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.roles._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection

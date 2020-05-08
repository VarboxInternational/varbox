@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('roles-add')
                @include('varbox::buttons.add', ['url' => route('admin.roles.create')])
            @endpermission

            @include('varbox::admin.roles._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.roles._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

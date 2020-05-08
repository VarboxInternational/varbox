@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('users-add')
                @include('varbox::buttons.add', ['url' => route('admin.users.create')])
            @endpermission

            @include('varbox::admin.users._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.users._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

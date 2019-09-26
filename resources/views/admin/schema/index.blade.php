@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('schema-add')
                @include('varbox::buttons.add', ['url' => route('admin.schema.create')])
            @endpermission

            @include('varbox::admin.schema._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.schema._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection

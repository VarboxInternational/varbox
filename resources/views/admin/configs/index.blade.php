@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('configs-add')
                @include('varbox::buttons.add', ['url' => route('admin.configs.create')])
            @endpermission

            @include('varbox::admin.configs._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.configs._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection


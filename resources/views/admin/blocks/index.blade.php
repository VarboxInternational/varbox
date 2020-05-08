@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('blocks-add')
                @include('varbox::buttons.add', ['url' => route('admin.blocks.create')])
            @endpermission

            @include('varbox::admin.blocks._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.blocks._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

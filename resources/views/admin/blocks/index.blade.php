@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('blocks-add')
                {!! button()->addRecord(route('admin.blocks.create')) !!}
            @endpermission

            @include('varbox::admin.blocks._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.blocks._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection

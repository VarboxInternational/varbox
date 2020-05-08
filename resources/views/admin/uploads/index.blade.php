@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.uploads._upload')

    <div class="row row-cards">
        <div class="col-lg-3">
            @include('varbox::admin.uploads._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.uploads._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection


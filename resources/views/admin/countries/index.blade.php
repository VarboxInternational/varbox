@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('countries-add')
                @include('varbox::buttons.add', ['url' => route('admin.countries.create')])
            @endpermission

            @include('varbox::admin.countries._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.countries._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('countries-add')
                        @include('varbox::buttons.add', ['url' => route('admin.countries.create')])
                    @endpermission
                    @permission('countries-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.countries.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.countries._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.countries._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('cities-add')
                        @include('varbox::buttons.add', ['url' => route('admin.cities.create')])
                    @endpermission
                    @permission('cities-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.cities.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.cities._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.cities._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('states-add')
                        @include('varbox::buttons.add', ['url' => route('admin.states.create')])
                    @endpermission
                    @permission('states-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.states.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.states._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.states._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

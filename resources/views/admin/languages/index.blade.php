@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('languages-add')
                        @include('varbox::buttons.add', ['url' => route('admin.languages.create')])
                    @endpermission
                    @permission('languages-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.languages.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.languages._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.languages._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

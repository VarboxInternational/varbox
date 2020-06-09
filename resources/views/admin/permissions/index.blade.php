@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('permissions-add')
                        @include('varbox::buttons.add', ['url' => route('admin.permissions.create')])
                    @endpermission
                    @permission('permissions-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.permissions.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.permissions._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.permissions._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

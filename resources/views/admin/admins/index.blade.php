@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('admins-add')
                        @include('varbox::buttons.add', ['url' => route('admin.admins.create')])
                    @endpermission
                    @permission('users-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.admins.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.admins._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.admins._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

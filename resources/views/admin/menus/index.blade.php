@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('menus-add')
                        @include('varbox::buttons.add', ['url' => route('admin.menus.create', $location)])
                    @endpermission
                    @permission('menus-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.menus.csv', ['location' => $location] + request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.menus._tree')
            @include('varbox::admin.menus._filter')
        </div>
        <div class="col-lg-9">
            <div class="js-TreeContainer"></div>
        </div>
    </div>
@endsection


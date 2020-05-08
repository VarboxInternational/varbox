@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('menus-add')
                @include('varbox::buttons.add', ['url' => route('admin.menus.create', $location)])
            @endpermission

            @include('varbox::admin.menus._tree')
            @include('varbox::admin.menus._filter')
        </div>
        <div class="col-lg-9">
            <div class="js-TreeContainer"></div>
        </div>
    </div>
@endsection


@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('pages-add')
                {!! button()->addRecord(route('admin.pages.create')) !!}
            @endpermission

            @include('varbox::admin.pages._tree')
            @include('varbox::admin.pages._filter')
        </div>
        <div class="col-lg-9">
            <div class="js-TreeContainer"></div>
        </div>
    </div>
@endsection


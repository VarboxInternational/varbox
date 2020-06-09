@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('pages-add')
                        @include('varbox::buttons.add', ['url' => route('admin.pages.create')])
                    @endpermission
                    @permission('pages-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.pages.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.pages._tree')
            @include('varbox::admin.pages._filter')
        </div>
        <div class="col-lg-9">
            <div class="js-TreeContainer"></div>
        </div>
    </div>
@endsection


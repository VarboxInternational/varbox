@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('configs-add')
                        @include('varbox::buttons.add', ['url' => route('admin.configs.create')])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.configs._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.configs._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection


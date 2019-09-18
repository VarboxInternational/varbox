@extends('varbox::layouts.admin.default')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            @include('varbox::admin.analytics._metrics', ['metrics' => $metrics])
        </div>
    </div>
    @permission('analytics-edit')
        <div class="row row-cards">
            <div class="col-12">
                @include('varbox::admin.analytics._code', ['item' => $item])
            </div>
        </div>
    @endpermission
@endsection

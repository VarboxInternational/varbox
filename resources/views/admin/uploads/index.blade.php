@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.uploads._upload')

    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('uploads-export')
                        <a href="{{ route('admin.uploads.csv', request()->query()) }}" class="button-export btn btn-green btn-square btn-block">
                            <i class="fe fe-download mr-2"></i>Export Csv
                        </a>
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.uploads._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.uploads._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection


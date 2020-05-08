@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @hasanypermission(['redirects-add', 'redirects-delete'])
                <div class="card">
                    <div class="card-body">
                        @permission('redirects-add')
                            <a href="{{ route('admin.redirects.create') }}" class="button-add btn btn-primary btn-square btn-block">
                                <i class="fe fe-plus mr-2"></i>Add New
                            </a>
                        @endpermission
                        @permission('redirects-delete')
                            {!! form()->open(['url' => route('admin.redirects.delete_all'), 'method' => 'DELETE']) !!}
                            {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>Delete All', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block mt-5']) !!}
                            {!! form()->close() !!}
                        @endpermission
                    </div>
                </div>
            @endhasanypermission

            @include('varbox::admin.redirects._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.redirects._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

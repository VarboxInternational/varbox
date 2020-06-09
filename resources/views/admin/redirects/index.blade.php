@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @hasanypermission(['redirects-add', 'redirects-export', 'redirects-delete'])
                <div class="card">
                    <div class="card-body">
                        @permission('redirects-add')
                            @include('varbox::buttons.add', ['url' => route('admin.redirects.create')])
                        @endpermission
                        @permission('redirects-export')
                            {!! form()->open(['url' => route('admin.redirects.export'), 'method' => 'POST']) !!}
                            {!! form()->button('<i class="fe fe-upload mr-2"></i>Export File', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-green btn-square btn-block mt-5']) !!}
                            {!! form()->close() !!}
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

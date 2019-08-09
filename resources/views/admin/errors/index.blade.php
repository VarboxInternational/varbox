@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @permission('errors-delete')
                <div class="card">
                    <div class="card-body">
                        {!! form()->open(['url' => route('admin.errors.clean'), 'method' => 'DELETE']) !!}
                        {!! form()->button('<i class="fe fe-trash mr-2"></i>Delete Old Errors', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-yellow btn-square btn-block text-left mb-5', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Older than ' . $days . ' ' . Str::plural('day', $days)]) !!}
                        {!! form()->close() !!}

                        {!! form()->open(['url' => route('admin.errors.delete'), 'method' => 'DELETE']) !!}
                        {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>Delete All Errors', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block text-left']) !!}
                        {!! form()->close() !!}
                    </div>
                </div>
            @endpermission

            @include('varbox::admin.errors._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.errors._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection

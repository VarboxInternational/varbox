@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    {!! form()->open(['url' => route('admin.backups.store'), 'method' => 'POST']) !!}
                    {!! form()->button('<i class="fe fe-plus mr-2"></i>Create New Backup', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue btn-square btn-block text-left mb-5']) !!}
                    {!! form()->close() !!}

                    {!! form()->open(['url' => route('admin.backups.delete_all'), 'method' => 'DELETE']) !!}
                    {!! form()->button('<i class="fe fe-trash mr-2"></i>Delete All Backups', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block text-left']) !!}
                    {!! form()->close() !!}
                </div>
            </div>

            @include('varbox::admin.backups._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.backups._table')

            {!! $items->links('varbox::pagination.default', request()->query()) !!}
        </div>
    </div>
@endsection
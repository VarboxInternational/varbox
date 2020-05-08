@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @hasanypermission(['backups-create', 'backups-delete'])
                <div class="card">
                    <div class="card-body">
                        @permission('backups-create')
                            {!! form()->open(['url' => route('admin.backups.store'), 'method' => 'POST']) !!}
                            {!! form()->button('<i class="fe fe-plus mr-2"></i>Create New Backup', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue btn-square btn-block text-left mb-5']) !!}
                            {!! form()->close() !!}
                        @endpermission

                        @permission('backups-delete')
                            {!! form()->open(['url' => route('admin.backups.clean'), 'method' => 'DELETE']) !!}
                            {!! form()->button('<i class="fe fe-trash mr-2"></i>Delete Old Backups', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-yellow btn-square btn-block text-left mb-5', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Older than ' . $days . ' ' . Str::plural('day', $days)]) !!}
                            {!! form()->close() !!}

                            {!! form()->open(['url' => route('admin.backups.delete'), 'method' => 'DELETE']) !!}
                            {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>Delete All Backups', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block text-left']) !!}
                            {!! form()->close() !!}
                        @endpermission
                    </div>
                </div>
            @endpermission

            @include('varbox::admin.backups._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.backups._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

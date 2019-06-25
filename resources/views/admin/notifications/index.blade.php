@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @if(!$isAnotherUser)
                <div class="card">
                    <div class="card-body">
                        {!! form()->open(['url' => route('admin.notifications.mark_all_as_read'), 'method' => 'POST']) !!}
                        {!! form()->button('<i class="fe fe-check mr-2"></i>Mark All As Read', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue btn-square btn-block mb-5 text-left']) !!}
                        {!! form()->close() !!}

                        {!! form()->open(['url' => route('admin.notifications.delete_read'), 'method' => 'DELETE']) !!}
                        {!! form()->button('<i class="fe fe-eye-off mr-2"></i>Delete Read Notifications', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-green btn-square btn-block mb-5 text-left']) !!}
                        {!! form()->close() !!}

                        {!! form()->open(['url' => route('admin.notifications.delete_old'), 'method' => 'DELETE']) !!}
                        {!! form()->button('<i class="fe fe-trash mr-2"></i>Delete Old Notifications', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-yellow btn-square btn-block mb-5 text-left', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Older than ' . $days . ' ' . Str::plural('day', $days)]) !!}
                        {!! form()->close() !!}

                        {!! form()->open(['url' => route('admin.notifications.delete_all'), 'method' => 'DELETE']) !!}
                        {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>Delete All Notifications', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block text-left']) !!}
                        {!! form()->close() !!}
                    </div>
                </div>
            @endif

            @include('varbox::admin.notifications._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.notifications._table')

            {!! $items->links("varbox::helpers.pagination.admin", request()->all()) !!}
        </div>
    </div>
@endsection
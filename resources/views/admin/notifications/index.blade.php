@extends('varbox::layouts.admin.default')

@section('title', $title)

@if($isAnotherUser)
    @section('top')
        <div class="alert alert-info col-lg-12 mb-5">
            <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
                <i class="fe fe-info mr-2" aria-hidden="true"></i>
            </div>
            <div class="d-inline-block">
                <h4>You are now viewing another user's notifications!</h4>
                <p class="mb-0">Interactive actions are not available.</p>
            </div>
        </div>
    @endsection
@endif

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            @if(!$isAnotherUser)
                @hasanypermission(['notifications-read', 'notifications-delete'])
                    <div class="card">
                        <div class="card-body">
                            @permission('notifications-read')
                                {!! form()->open(['url' => route('admin.notifications.mark_all_as_read'), 'method' => 'POST']) !!}
                                {!! form()->button('<i class="fe fe-check mr-2"></i>Mark All As Read', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue btn-square btn-block text-left']) !!}
                                {!! form()->close() !!}
                            @endpermission
                            @permission('notifications-delete')
                                {!! form()->open(['url' => route('admin.notifications.delete_read'), 'method' => 'DELETE']) !!}
                                {!! form()->button('<i class="fe fe-eye-off mr-2"></i>Delete Read Notifications', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-green btn-square btn-block mt-5 text-left']) !!}
                                {!! form()->close() !!}
                            @endpermission
                            @permission('notifications-delete')
                                {!! form()->open(['url' => route('admin.notifications.delete_old'), 'method' => 'DELETE']) !!}
                                {!! form()->button('<i class="fe fe-trash mr-2"></i>Delete Old Notifications', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-yellow btn-square btn-block mt-5 text-left', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Older than ' . $days . ' ' . Str::plural('day', $days)]) !!}
                                {!! form()->close() !!}
                            @endpermission
                            @permission('notifications-delete')
                                {!! form()->open(['url' => route('admin.notifications.delete_all'), 'method' => 'DELETE']) !!}
                                {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>Delete All Notifications', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block mt-5 text-left']) !!}
                                {!! form()->close() !!}
                            @endpermission
                        </div>
                    </div>
                @endhasanypermission
            @endif

            @include('varbox::admin.notifications._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.notifications._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection

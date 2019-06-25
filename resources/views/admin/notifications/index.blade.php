@extends('varbox::layouts.admin.default')

@section('content')
    <section class="filters">
        @include('varbox::admin.audit.notifications._filter')
    </section>

    <section class="content content-quarter one">
        {!! form()->open(['url' => route('admin.notifications.mark_all_as_read'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-check-circle"></i>&nbsp; Mark all notifications as read', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to mark all your notifications as being read?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-quarter two">
        {!! form()->open(['url' => route('admin.notifications.delete_read'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-eye-slash"></i>&nbsp; Discard already read notifications', ['type' => 'submit', 'class' => 'btn green full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all your read notifications?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-quarter three">
        {!! form()->open(['url' => route('admin.notifications.delete_old'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup notifications older than ' . (int)config('varbox.audit.notification.delete_records_older_than', 30) . ' days', ['type' => 'submit', 'class' => 'btn yellow full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all of the notifications older than the given time limit?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-quarter four">
        {!! form()->open(['url' => route('admin.notifications.delete_all'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Delete all notifications', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to delete absolutely all of your notifications?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('varbox::admin.audit.notifications._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
    </section>
@endsection

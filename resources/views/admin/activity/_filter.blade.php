{!! form()->open(['url' => request()->url(), 'method' => 'get', 'class' => 'card ' . (empty(request()->except(['page', 'sort', 'dir'])) ? 'card-collapsed' : '')]) !!}
<div class="filter-records-container card-header" data-toggle="card-collapse" style="cursor: pointer;">
    <h3 class="card-title">Filter Records</h3>
    <div class="card-options">
        <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
    </div>
</div>
<div class="card-body">
    {!! form_admin()->select('user', 'User', ['' => 'All Users'] + $users->pluck('email', 'id')->toArray(), request()->query('user') ?: null) !!}
    <div class="row">
        <div class="col">
            {!! form_admin()->select('entity', 'Entity', ['' => 'All Entities'] + $entities, request()->query('entity') ?: null) !!}
        </div>
        <div class="col">
            {!! form_admin()->select('event', 'Event', ['' => 'All Events'] + $events, request()->query('event') ?: null) !!}
        </div>
    </div>
    <div class="row">
        <div class="col">
            {!! form_admin()->date('start_date', 'From', request()->query('start_date') ?: null) !!}
        </div>
        <div class="col">
            {!! form_admin()->date('end_date', 'To', request()->query('end_date') ?: null) !!}
        </div>
    </div>
</div>
<div class="card-footer text-right">
    {!! button()->clearFilters() !!}
    {!! button()->filterRecords() !!}
</div>
{!! form()->close() !!}

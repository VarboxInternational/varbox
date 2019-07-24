{!! form()->open(['url' => request()->url(), 'method' => 'get', 'class' => 'card ' . (empty(request()->except(['page'])) ? 'card-collapsed' : '')]) !!}
<div class="filter-records-container card-header" data-toggle="card-collapse" style="cursor: pointer;">
    <h3 class="card-title">Filter Records</h3>
    <div class="card-options">
        <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
    </div>
</div>
<div class="card-body">
    {!! form_admin()->text('search', 'Keyword', request()->query('search') ?: null) !!}
    {!! form_admin()->select('type', 'Type', ['' => 'All Types'] + $types, request()->query('type') ?: null) !!}
    <div class="row">
        <div class="col">
            {!! form_admin()->select('published', 'Publish', ['' => '---', 1 => 'Published', 0 => 'Not Published'], request()->query('published') ?: null, ['style' => 'width: 48%;']) !!}
        </div>
        <div class="col">
            {!! form_admin()->select('trashed', 'Trash', ['' => 'Un/Del', 1 => 'Trashed', 0 => 'Not Trashed'], request()->query('trashed') ?: null, ['style' => 'width: 48%;']) !!}
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
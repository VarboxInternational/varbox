{!! form()->open(['url' => request()->url(), 'method' => 'get', 'class' => 'card ' . (empty(request()->except(['page', 'sort', 'dir'])) ? 'card-collapsed' : '')]) !!}
    <div class="filter-records-container card-header" data-toggle="card-collapse" style="cursor: pointer;">
        <h3 class="card-title">Filter Records</h3>
        <div class="card-options">
            <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
    </div>
    <div class="card-body">
        {!! form_admin()->text('search', 'Keyword', request()->query('search') ?: null) !!}
        {!! form_admin()->select('guard', 'Guard', ['' => 'All'] + $guards, request()->query('guard') ?: null) !!}
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
{!! form()->open(['url' => request()->url(), 'method' => 'get', 'class' => 'card ' . (empty(request()->except(['page'])) ? 'card-collapsed' : '')]) !!}
<div class="filter-records-container card-header" data-toggle="card-collapse" style="cursor: pointer;">
    <h3 class="card-title">Filter Records</h3>
    <div class="card-options">
        <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
    </div>
</div>
<div class="card-body">
    {!! form_admin()->text('search', 'Keyword', request()->query('search') ?: null) !!}
    <div class="row">
        <div class="col">
            {!! form_admin()->select('default', 'Default', ['' => '---', true => 'Yes', false => 'No'], request()->has('default') ? request()->query('default') : null) !!}
        </div>
        <div class="col">
            {!! form_admin()->select('active', 'Active', ['' => '---', true => 'Yes', false => 'No'], request()->has('active') ? request()->query('active') : null) !!}
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
    @include('varbox::buttons.clear')
    @include('varbox::buttons.filter')
</div>
{!! form()->close() !!}

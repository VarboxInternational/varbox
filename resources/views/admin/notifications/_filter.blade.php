{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->select('read', ['' => 'Show All', '1' => 'Un-read Only', '2' => 'Read Only'], request()->query('read') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form_admin()->calendar('start_date', false, request()->query('start_date') !== null ? request()->query('start_date') : null, ['placeholder' => 'Date From', 'style' => 'width: 48%;']) !!}
        {!! form_admin()->calendar('end_date', false, request()->query('end_date') !== null ? request()->query('end_date') : null, ['placeholder' => 'Date To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <div>
        {!! button()->filterRecords() !!}
        {!! button()->clearFilters() !!}
    </div>
{!! form()->close() !!}

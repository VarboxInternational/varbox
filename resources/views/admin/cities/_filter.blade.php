{!! form()->open(['url' => request()->url(), 'method' => 'get', 'class' => 'card ' . (empty(request()->except(['page'])) ? 'card-collapsed' : '')]) !!}
<div class="filter-records-container card-header" data-toggle="card-collapse" style="cursor: pointer;">
    <h3 class="card-title">Filter Records</h3>
    <div class="card-options">
        <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
    </div>
</div>
<div class="card-body">
    {!! form_admin()->text('search', 'Keyword', request()->query('search') ?: null) !!}
            {!! form_admin()->select('country', 'Country', ['' => 'All Countries'] + $countries->pluck('name', 'id')->toArray(), request()->query('country') ?: null) !!}
            {!! form_admin()->select('state', 'State', ['' => 'All States'] + $states->pluck('name', 'id')->toArray(), request()->query('state') ?: null) !!}
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

@push('scripts')
    <script type="text/javascript">
        var country = $('select[name="country"]');
        var state = $('select[name="state"]');

        var getStates = function () {
            var url = '{{ route('admin.states.get') }}' + '/' + country.val();
            var select = $('select[name="state"]');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(data) {
                    var selected = @json(request()->query('state'))

                    if (data.status === true) {
                        select.empty();

                        select.append('<option value="">None</option>');

                        $.each(data.states, function (index, state) {
                            select.append('<option value="' + state.id + '"' + (state.id == selected ? ' selected="selected"' : '') + '>' + state.name + '</option>');
                        });
                    }
                }
            });
        };

        if (country.length) {
            if (country.val()) {
                getStates();
            } else {
                state.empty();
            }

            country.change(function () {
                if (country.val()) {
                    getStates();
                } else {
                    state.empty();
                }
            });
        }
    </script>
@endpush
{!! validation('admin')->errors() !!}

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'put', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'post', 'class' => 'frm row row-cards', 'files' => true]) !!}
@endif
<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-blue"></div>
        <div class="card-header">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {!! form_admin()->text('name', 'Name', null, ['required']) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->select('country_id', 'Country', ['' => 'Please select'] + $countries->pluck('name', 'id')->toArray(), null, ['required']) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->select('state_id', 'State', ['' => 'None'] + ($item->exists && isset($states) ? $states->pluck('name', 'id')->toArray() : [])) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                @include('varbox::buttons.cancel', ['url' => route('admin.cities.index')])
                @if($item->exists)
                    @include('varbox::buttons.save_stay')
                @else
                    @include('varbox::buttons.save_new')
                    @include('varbox::buttons.save_continue', ['route' => 'admin.cities.edit'])
                @endif
                @include('varbox::buttons.save')
            </div>
        </div>
    </div>
</div>
{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.city_form_request', \Varbox\Requests\CityRequest::class), '.frm') !!}

    <script type="text/javascript">
        var country = $('select[name="country_id"]');
        var state = $('select[name="state_id"]');

        var getStates = function () {
            var url = '{{ route('admin.states.get') }}' + '/' + country.val();
            var select = $('select[name="state_id"]');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(data) {
                    if (data.status === true) {
                        select.empty();

                        select.append('<option value="">None</option>');

                        $.each(data.states, function (index, state) {
                            select.append('<option value="' + state.id + '">' + state.name + '</option>');
                        });
                    }
                }
            });
        };

        if (country.length) {
            @if(!$item->exists)
                if (country.val()) {
                    getStates();
                } else {
                    state.empty();
                }
            @endif

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

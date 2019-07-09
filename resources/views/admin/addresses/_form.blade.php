{!! validation('admin')->errors() !!}

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'put', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'post', 'class' => 'frm row row-cards', 'files' => true]) !!}
@endif

{!! form()->hidden('user_id', $user->id) !!}

<div class="col-md-6">
    <div class="card">
        <div class="card-status bg-blue"></div>
        <div class="card-header">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {!! form_admin()->textarea('address', 'Address', null, ['required', 'style' => 'height: 200px;']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Location Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {!! form_admin()->select('country_id', 'Country', ['' => 'Please select'] + $countries->pluck('name', 'id')->toArray()) !!}
                </div>
                <div class="col-12">
                    {!! form_admin()->select('state_id', 'State', ['' => 'None'] + ($item->exists && isset($states) ? $states->pluck('name', 'id')->toArray() : [])) !!}
                </div>
                <div class="col-12">
                    {!! form_admin()->select('city_id', 'City', ['' => 'None'] + ($item->exists && isset($cities) ? $cities->pluck('name', 'id')->toArray() : [])) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                {!! button()->cancelAction(route('admin.addresses.index', $user->getKey())) !!}
                @if($item->exists)
                    {!! button()->saveAndStay() !!}
                @else
                    {!! button()->saveAndNew() !!}
                    {!! button()->saveAndContinue('admin.addresses.edit', ['user' => $user->id]) !!}
                @endif
                {!! button()->saveRecord() !!}
            </div>
        </div>
    </div>
</div>
{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.address_form_request', \Varbox\Requests\AddressRequest::class), '.frm') !!}

    <script type="text/javascript">
        var countrySelect = $('select[name="country_id"]');
        var stateSelect = $('select[name="state_id"]');
        var citySelect = $('select[name="city_id"]');

        countrySelect.change(function () {
            if ($(this).val()) {
                getStates($(this).val());
            } else {
                stateSelect.empty();
                citySelect.empty();
            }
        });

        stateSelect.change(function () {
            if ($(this).val()) {
                getCities(countrySelect.val(), $(this).val());
            } else {
                citySelect.empty();
            }
        });

        var getStates = function (countryId) {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.states.get') }}' + '/' + countryId,
                success : function(data) {
                    if (data.status == true) {
                        stateSelect.empty();
                        citySelect.empty();

                        stateSelect.append('<option value="">None</option>');
                        citySelect.append('<option value="">None</option>');

                        $.each(data.states, function (index, state) {
                            stateSelect.append('<option value="' + state.id + '">' + state.name + '</option>');
                        });

                        $.each(data.cities, function (index, city) {
                            citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not load the states! Please try again.');
                }
            });
        }, getCities = function (countryId, stateId) {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.cities.get') }}' + '/' + countryId + '/' + stateId,
                success : function(data) {
                    if (data.status == true) {
                        citySelect.empty();

                        citySelect.append('<option value="">None</option>');

                        $.each(data.cities, function (index, city) {
                            citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });
                    }
                },
                error: function (err) {
                    init.FlashMessage('error', 'Could not load the cities! Please try again.');
                }
            });
        };
    </script>
@endpush

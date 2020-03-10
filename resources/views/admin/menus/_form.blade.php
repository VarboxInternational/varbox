{!! validation('admin')->errors() !!}

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'frm row row-cards', 'files' => true]) !!}
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
                    {!! form_admin()->text('name', 'Name', null, ['required', 'class' => 'js-SlugFrom']) !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->select('type', 'Type', [null => ''] + $types, null, ['required', 'class' => 'js-MenuType']) !!}
                </div>
                <div class="js-MenuUrl js-MenuUrlUrl col-md-4 d-none">
                    {!! form_admin()->text('url', 'Url', null, ['required']) !!}
                </div>
                <div class="js-MenuUrl js-MenuUrlRoute col-md-4 d-none">
                    {!! form_admin()->select('route', 'Url', [], null, ['required']) !!}
                </div>
                <div class="js-MenuUrl js-MenuUrlCustom col-md-4 d-none">
                    {!! form_admin()->select('menuable_id', 'Url', [], null, ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Secondary Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {!! form_admin()->yesno('active', false, 'Is active?') !!}
                </div>
                <div class="col-md-4">
                    {!! form_admin()->yesno('data[new_window]', false, 'Open in new window?') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                @include('varbox::buttons.cancel', ['url' => route('admin.menus.index', $location)])
                @if($item->exists)
                    @include('varbox::buttons.save_stay')
                @else
                    @include('varbox::buttons.save_new')
                    @include('varbox::buttons.save_continue', ['route' => 'admin.menus.edit', 'parameters' => ['location' => $location]])
                @endif
                @include('varbox::buttons.save')
            </div>
        </div>
    </div>
</div>

{!! form_admin()->close() !!}

@push('scripts')
    {{--{!! JsValidator::formRequest(config('varbox.bindings.form_requests.menu_form_request', \Varbox\Requests\MenuRequest::class), '.frm') !!}--}}

    <script type="text/javascript">
        $(function () {
            let menuTypeSelector = '.js-MenuType',
                menuUrlSelector = '.js-MenuUrl',
                menuUrlUrlSelector = '.js-MenuUrlUrl',
                menuUrlRouteSelector = '.js-MenuUrlRoute',
                menuUrlCustomSelector = '.js-MenuUrlCustom',
                menuRouteSelectSelector = 'select[name="route"]',
                menuUrlSelectSelector = 'select[name="menuable_id"]';

            let selectMenuType = function (type) {
                $(menuUrlSelector).addClass('d-none');

                if (type.val() == 'url') {
                    $(menuUrlUrlSelector).removeClass('d-none');

                    return false;
                }

                if (type.val() == 'route') {
                    $(menuRouteSelectSelector).empty().trigger("change");

                    $.ajax({
                        type: 'GET',
                        url: '{{ route('admin.menus.route') }}',
                        dataType: 'json',
                        success: function(data) {
                            let selected = @json($item->exists ? $item->route : null)

                            if (data.status == true) {
                                $.each(data.attributes, function (index, attribute){
                                    $(menuRouteSelectSelector).append(
                                        '<option value="' + attribute.value + '"' + (
                                            attribute.value == selected ? ' selected' : ''
                                        ) + '>' + attribute.name + '</option>'
                                    );
                                });

                                $(menuRouteSelectSelector).trigger("change");
                            }

                            $(menuUrlRouteSelector).removeClass('d-none');
                        }
                    });

                    return false;
                }

                $(menuUrlSelectSelector).empty().trigger("change");

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.menus.entity') }}' + '/' + type.val(),
                    dataType: 'json',
                    success: function(data) {
                        let selected = @json($item->exists ? $item->menuable_id : null)

                        if (data.status == true) {
                            $.each(data.attributes, function (index, attribute){
                                $(menuUrlSelectSelector).append(
                                    '<option value="' + attribute.value + '"' + (
                                        attribute.value == selected ? ' selected' : ''
                                    ) + '>' + attribute.name + '</option>'
                                );
                            });

                            $(menuUrlSelectSelector).trigger("change");
                        }

                        $(menuUrlCustomSelector).removeClass('d-none');
                    }
                });

                return false;
            };

            if ($(menuTypeSelector).val() != '') {
                selectMenuType($(menuTypeSelector));
            }

            $(document).on('change', menuTypeSelector, function () {
                selectMenuType($(this));
            });
        });
    </script>
@endpush

@include('varbox::validation')

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'put', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'post', 'class' => 'frm row row-cards', 'files' => true]) !!}
@endif
<div class="col-12">
    <div class="card">
        <div class="card-status bg-blue"></div>
        <div class="card-header">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    {!! form_admin()->text('name', 'Name', null, ['required']) !!}
                </div>
                <div class="col-6">
                    {!! form_admin()->select('guard', 'Guard', $guards, null, ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Manage Permissions</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="permissions" id="admin-permissions">
                        @if($adminPermissions->count())
                            <div class="card-columns">
                                @foreach($adminPermissions as $name => $group)
                                    <div class="card form-fieldset p-0">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="form-label border-bottom border-primary mb-2 pb-1 text-primary">{{ $name }}</div>
                                                <div class="custom-controls-stacked">
                                                    {!! form_admin()->noWrap()->checkbox('dummy', false, 'All') !!}
                                                    @foreach($group as $permission)
                                                        {!! form_admin()->noWrap()->checkbox('permissions[]', false, $permission->label, $permission->id) !!}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray">No permissions for the <span class="text-primary">ADMIN</span> guard.</p>
                        @endif
                    </div>
                    <div class="permissions" id="web-permissions">
                        @if($webPermissions->count())
                            <div class="card-columns">
                                @foreach($webPermissions as $name => $group)
                                    <div class="card form-fieldset p-0">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="form-label border-bottom border-primary mb-2 pb-1 text-primary">{{ $name }}</div>
                                                <div class="custom-controls-stacked">
                                                    {!! form_admin()->noWrap()->checkbox('dummy', false, 'All') !!}
                                                    @foreach($group as $permission)
                                                        {!! form_admin()->noWrap()->checkbox('permissions[]', false, $permission->label, $permission->id) !!}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray">No permissions for the <span class="text-primary">WEB</span> guard.</p>
                        @endif
                    </div>
                    <div class="permissions" id="api-permissions">
                        @if($apiPermissions->count())
                            <div class="card-columns">
                                @foreach($apiPermissions as $name => $group)
                                    <div class="card form-fieldset p-0">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="form-label border-bottom border-primary mb-2 pb-1 text-primary">{{ $name }}</div>
                                                <div class="custom-controls-stacked">
                                                    {!! form_admin()->noWrap()->checkbox('dummy', false, 'All') !!}
                                                    @foreach($group as $permission)
                                                        {!! form_admin()->noWrap()->checkbox('permissions[]', false, $permission->label, $permission->id) !!}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray">No permissions for the <span class="text-primary">API</span> guard.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                @include('varbox::buttons.cancel', ['url' => route('admin.roles.index')])
                @if($item->exists)
                    @include('varbox::buttons.save_stay')
                @else
                    @include('varbox::buttons.save_new')
                    @include('varbox::buttons.save_continue', ['route' => 'admin.roles.edit'])
                @endif
                @include('varbox::buttons.save')
            </div>
        </div>
    </div>
</div>
{!! form_admin()->close() !!}

@push('scripts')
    <script type="text/javascript">
        switchPermissions($('select[name="guard"]').val());

        $('select[name="guard"]').change(function () {
            switchPermissions($(this).val());
        });

        $('input[type="checkbox"][name="dummy"]').click(function () {
            checkMultiplePermissions($(this));
        });

        function checkMultiplePermissions(checker) {
            checker.closest('.custom-controls-stacked').each(function (index, selector) {
                $(selector).find('input[type="checkbox"]').prop('checked', checker.prop('checked'));
            });
        }

        function switchPermissions(guard) {
            $('.permissions').hide();
            $('#' + guard + '-permissions').show();

            $('.permissions:not(#' + guard + '-permissions)')
                .find('input[type="checkbox"][name="permissions[]"], input[type="checkbox"][name="dummy"]').prop('checked', false);
        }
    </script>
@endpush


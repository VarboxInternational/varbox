{!! validation('admin')->errors() !!}

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
                <div class="col-md-6">
                    {!! form_admin()->text('email', 'Email', null, ['required', 'autocomplete' => 'off']) !!}
                    {!! form_admin()->select('roles[]', 'Roles', $roles->pluck('name', 'id'), $item->exists ? $item->roles->pluck('id') : null, ['multiple']) !!}
                </div>
                <div class="col-md-6">
                    {!! form_admin()->password('password', 'Password', ['required', 'placeholder' => 'Leave blank to remain the same', 'autocomplete' => 'off'], true) !!}
                    {!! form_admin()->password('password_confirmation', 'Re-Password', ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Personal Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    {!! form_admin()->text('first_name', 'First Name', null, ['required']) !!}
                </div>
                <div class="col-md-6">
                    {!! form_admin()->text('last_name', 'Last Name', null, ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-status bg-red"></div>
        <div class="card-header">
            <h3 class="card-title">Status Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {!! form_admin()->yesno('active', false, 'Is active?') !!}
                </div>
            </div>
        </div>
    </div>
</div>
@if($item->exists)
    @permission('addresses-list')
        <div class="col-12">
            <div class="card">
                <div class="card-status bg-yellow"></div>
                <div class="card-header border-0 py-5">
                    <h3 class="card-title">Addresses</h3>

                    <a href="{{ route('admin.addresses.index', $item->getKey()) }}" class="btn btn-yellow btn-square float-right ml-auto">
                        <i class="fe fe-eye mr-2"></i>View User's Addresses
                    </a>
                </div>
            </div>
        </div>
    @endpermission
@endif
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                @include('varbox::buttons.cancel', ['url' => route('admin.users.index')])
                @if($item->exists)
                    @include('varbox::buttons.save_stay')
                @else
                    @include('varbox::buttons.save_new')
                    @include('varbox::buttons.save_continue', ['route' => 'admin.users.edit'])
                @endif
                @include('varbox::buttons.save')
            </div>
        </div>
    </div>
</div>
{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.user_form_request', Varbox\Requests\UserRequest::class), '.frm') !!}
@endpush

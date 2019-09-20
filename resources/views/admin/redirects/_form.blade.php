{!! validation('admin')->errors() !!}

@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'put', 'class' => 'frm row row-cards', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'post', 'class' => 'frm row row-cards', 'files' => true]) !!}
@endif
<div class="col-md-6">
    <div class="card">
        <div class="card-status bg-blue"></div>
        <div class="card-header">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {!! form_admin()->text('old_url', 'Old URL', null, ['required']) !!}
                    {!! form_admin()->text('new_url', 'New URL', null, ['required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Status Info</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-secondary col-lg-12 mb-5">
                        <div class="d-inline-block">
                            <p class="mb-1 mt-1">This is used for the HTTP response code.</p>
                        </div>
                    </div>

                    {!! form_admin()->select('status', 'Status', $statuses) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                @include('varbox::buttons.cancel', ['url' => route('admin.redirects.index')])
                @if($item->exists)
                    @include('varbox::buttons.save_stay')
                @else
                    @include('varbox::buttons.save_new')
                    @include('varbox::buttons.save_continue', ['route' => 'admin.redirects.edit'])
                @endif
                @include('varbox::buttons.save')
            </div>
        </div>
    </div>
</div>
{!! form_admin()->close() !!}

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.bindings.form_requests.redirect_form_request', Varbox\Requests\RedirectRequest::class), '.frm') !!}
@endpush

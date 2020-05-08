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
                <div class="col-lg-8">
                    {!! form_admin()->textarea('value', 'Value') !!}
                </div>
                <div class="col-lg-4">
                    <h4 class="text-muted mt-lg-6 mt-md-4">Key</h4>
                    <span class="badge badge-info" style="font-size: 95%">
                        {{ $item->key ?: 'N/A' }}
                    </span>
                    <h4 class="text-muted mt-lg-6 mt-md-4">Group</h4>
                    <span class="badge badge-success" style="font-size: 95%">
                        {{ $item->group ?: 'N/A' }}
                    </span>
                    <h4 class="text-muted mt-lg-6 mt-md-4">Locale</h4>
                    <span class="badge badge-danger" style="font-size: 95%">
                        {{ strtoupper($item->locale ?: 'N/A') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex text-left">
                @include('varbox::buttons.cancel', ['url' => route('admin.translations.index')])
                @if($item->exists)
                    @include('varbox::buttons.save_stay')
                @else
                    @include('varbox::buttons.save_new')
                    @include('varbox::buttons.save_continue', ['route' => 'admin.translations.edit'])
                @endif
                @include('varbox::buttons.save')
            </div>
        </div>
    </div>
</div>
{!! form_admin()->close() !!}

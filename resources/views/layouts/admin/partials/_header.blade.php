<div class="header py-4 bg-blue-dark">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="{{ route('admin') }}">
                <img src="{{ asset('/vendor/varbox/images/logo.svg') }}" class="header-brand-img" alt="{{ config('app.name') }}">
            </a>
            <div class="d-flex order-lg-2 ml-auto">
                <div class="nav-item d-none d-md-flex">
                    <a href="{{ config('app.url') }}" class="btn btn-sm btn-outline-primary btn-square" target="_blank">view site</a>
                </div>

                {{--@if(\Varbox::moduleEnabled('audit'))
                    @include('varbox::layouts.admin.partials._notifications')
                @endif--}}

                @include('varbox::layouts.admin.partials._profile')
            </div>
            <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                <span class="header-toggler-icon"></span>
            </a>
        </div>
    </div>
</div>
<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg">
                @include('varbox::layouts.admin.partials._menu')
            </div>
        </div>
    </div>
</div>
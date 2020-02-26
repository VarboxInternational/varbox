<div class="header py-4 bg-blue-dark">
    <div class="container">
        <div class="d-flex">
            <div class="d-flex order-lg-2 ml-auto">
                @include('varbox::layouts.admin.partials._languages')
                @include('varbox::layouts.admin.partials._notifications')
                @include('varbox::layouts.admin.partials._profile')

                <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                    <span class="header-toggler-icon"></span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg order-lg-first">
                @include('varbox::layouts.admin.partials._menu')
            </div>
        </div>
    </div>
</div>

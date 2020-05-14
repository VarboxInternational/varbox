@if(!(isset($hideTitle) && $hideTitle === true))
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body row p-2">
                    <div class="col-md-6">
                        <h1 class="page-title float-left">@yield('title', '')</h1>
                    </div>
                    <div class="col-md-6">
                        @include('varbox::layouts.partials._count')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

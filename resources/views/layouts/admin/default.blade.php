<!DOCTYPE html>
<html lang="en">
<head>
    @include('varbox::layouts.admin.partials._head')
</head>
<body>
    <div class="page">
        <div class="page-main">
            @include('varbox::layouts.admin.partials._header')

            <div class="my-3 my-md-5">
                <div class="container">
                    @include('varbox::layouts.admin.partials._flash')

                    @yield('top')

                    @include('varbox::layouts.admin.partials._title')

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @include('varbox::layouts.admin.partials._footer')
</body>
</html>





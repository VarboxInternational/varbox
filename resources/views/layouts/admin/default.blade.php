<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    @include('varbox::layouts.admin.partials._head')
</head>
<body>
    <div class="page">
        <div class="page-main">
            @include('varbox::layouts.admin.partials._header')

            <div class="my-3 my-md-5">
                <div class="container">
                    @include('varbox::layouts.admin.partials._top')

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @include('varbox::layouts.admin.partials._footer')
</body>
</html>





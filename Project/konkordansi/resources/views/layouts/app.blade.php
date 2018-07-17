<!doctype html>
<html lang="en">
<head>
    @include('layouts.header')
</head>
<body>
<div class="wrapper">
    <div class="main-panel">
        @include('layouts.nav-horizontal')
        <div class="content">
            <div class="container-fluid">
                @include('layouts.message')
                @yield('content')
            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>
@include('layouts.footer_script')
@yield('footer_script')
</body>
</html>
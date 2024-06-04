<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('layouts.student.partials.css')

    <title>{{ $title ?? 'ISG' }}</title>

    @stack('styles')

</head>

<body class="crm_body_bg">

    {{-- sidebar --}}
    @include('layouts.student.partials.sidebar')

    <section class="main_content dashboard_part large_header_bg">

        @include('layouts.student.partials.navbar')

        @yield('content')

        {{-- footer section --}}
        {{-- @include('layouts.student.partials.footer') --}}
    </section>


    {{-- <div id="back-top" style="display: none;">
        <a title="Go to Top" href="#">
            <i class="ti-angle-up"></i>
        </a>
    </div> --}}

    @include('layouts.student.partials.js')


</body>

</html>

<!DOCTYPE html>
<html lang="fr">
@include('emails.include.head')

<body>
    <div class="wrapper">

        {{-- ── Header ── --}}
        @include('emails.include.header')

        {{-- ── Body ── --}}
        <div class="body">
            @yield('container')
        </div>

        {{-- ── Footer ── --}}
        @include('emails.include.footer')

    </div>
</body>

</html>

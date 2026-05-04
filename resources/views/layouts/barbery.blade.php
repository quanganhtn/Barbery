<!doctype html>
<html lang="vi" class="h-full scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', "Gentlemen's Barbershop")</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/png" href="{{ Voyager::image(setting('site.logo')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    @stack('head')
    <script>
        window.Barbery = {
            routes: {
                services: "/api/services",
                stylists: "/api/stylists",
                createBooking: "/api/bookings",
                bookedSlots: "/api/booked-slots",
                lookup: "/api/lookup",
                availableSlots: "/api/available-slots",
            }
        };
    </script>
</head>

<body class="h-full bg-darker font-body text-white">
    <div id="app" class="h-full overflow-auto">

        @include('partials.barbery.nav')
        @include('partials.barbery.mobile')

        <main id="main-content">
            @yield('content')
        </main>

        @include('partials.barbery.ui')
        @include('partials.barbery.chatbox')
    </div>


    @stack('scripts')
</body>

</html>

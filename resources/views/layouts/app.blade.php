<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Library')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar - deteksi role dari URL -->
    @if(request()->is('dashboard-admin') || request()->is('admin/*'))
        @include('layouts.sidebar_admin')
    @elseif(request()->is('dashboard-anggota') || request()->is('dashboard-anggota/*'))
        @include('layouts.sidebar_anggota')
    @endif

    <!-- Main Content -->
    <main class="flex-1 min-w-0 overflow-x-auto">
        <div class="p-6">
            @yield('content')
        </div>
    </main>

</div>

</body>
</html>
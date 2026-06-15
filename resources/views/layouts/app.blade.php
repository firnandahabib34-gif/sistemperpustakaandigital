<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Library')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 256px;
            height: 100vh;
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 256px;
            padding: 24px;
            min-height: 100vh;
        }
    </style>
</head>

<body>

@if(request()->is('dashboard-admin') || request()->is('admin/*'))
<div class="sidebar bg-blue-900 text-white flex flex-col h-full">
    @include('layouts.sidebar_admin')
</div>
@elseif(request()->is('dashboard-anggota') || request()->is('dashboard-anggota/*'))
    <div class="sidebar bg-blue-900 text-white flex flex-col h-full">
        @include('layouts.sidebar_anggota')
    </div>
@endif

<div class="main-content bg-gray-100">
    @yield('content')
</div>

</body>
</html>
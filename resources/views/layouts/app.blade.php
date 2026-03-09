<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @vite(['resources/css/login.css'])
    <style>
        :root { --global-topbar-h: 56px; }

        /* Samakan perilaku header dengan dashboard untuk halaman yang masih pakai layouts.app */
        body.has-global-topbar .topbar{
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
        }

        body.has-global-topbar .app{
            margin-top: var(--global-topbar-h) !important;
        }
    </style>
</head>
<body>

    @yield('content')

    <script>
        (function () {
            if (document.querySelector('.topbar')) {
                document.body.classList.add('has-global-topbar');
            }
        })();
    </script>
</body>
</html>

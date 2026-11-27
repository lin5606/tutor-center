{{-- TODO: Shared Bootstrap 5 shell, navigation, flash validation messages, and yielded page content. --}}
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Trung tâm gia sư')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Gia sư</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('classes.index') }}">Tìm lớp</a>
                <a class="nav-link" href="{{ route('tutors.index') }}">Gia sư</a>
                @auth
                    <a class="nav-link" href="{{ route('parent.classes.index') }}">Khu vực phụ huynh</a>
                    <a class="nav-link" href="{{ route('tutor.profile.edit') }}">Khu vực gia sư</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-link nav-link" type="submit">Đăng xuất</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                    <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

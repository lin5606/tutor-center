@extends('layouts.app')

{{-- TODO: Extend the shared layout with admin navigation shown only for granted permission slugs. --}}
@section('content')
    <div class="row">
        <aside class="col-md-3 mb-3">
            <nav class="list-group">
                @if (auth()->user()?->hasPermission('dashboard.view'))
                    <a class="list-group-item list-group-item-action" href="{{ route('admin.dashboard') }}">Tổng quan</a>
                @endif
                @if (auth()->user()?->hasPermission('users.manage'))
                    <a class="list-group-item list-group-item-action" href="{{ route('admin.users_roles.index') }}">Tài khoản & vai trò</a>
                @endif
                @if (auth()->user()?->hasPermission('tutors.approve'))
                    <a class="list-group-item list-group-item-action" href="{{ route('admin.tutors.index') }}">Duyệt gia sư</a>
                @endif
                @if (auth()->user()?->hasPermission('classes.manage'))
                    <a class="list-group-item list-group-item-action" href="{{ route('admin.classes.index') }}">Quản lý lớp</a>
                @endif
                @if (auth()->user()?->hasPermission('payments.manage'))
                    <a class="list-group-item list-group-item-action" href="{{ route('admin.payments_refunds.index') }}">Thanh toán & hoàn phí</a>
                @endif
            </nav>
        </aside>
        <section class="col-md-9">
            @yield('admin-content')
        </section>
    </div>
@endsection

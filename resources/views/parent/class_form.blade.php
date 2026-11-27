@extends('layouts.app')

@section('title', isset($class) ? 'Sửa yêu cầu tìm gia sư' : 'Đăng yêu cầu tìm gia sư')
@section('content')
    {{-- TODO: Build class fields and repeatable schedule rows; collect private address separately from public district. --}}
    <h1>{{ isset($class) ? 'Cập nhật yêu cầu' : 'Đăng yêu cầu tìm gia sư' }}</h1>
@endsection

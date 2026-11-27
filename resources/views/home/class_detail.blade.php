@extends('layouts.app')

@section('title', $class->title)
@section('content')
    {{-- TODO: Show public class details and weekly schedule; private_address must not be rendered here. --}}
    <h1>{{ $class->title }}</h1>
@endsection

@extends('layouts.app')

@section('content')
    <h1>Halaman Dashboard User</h1>
    <p>Selamat datang, {{ Auth::user()->name }}!</p>
@endsection

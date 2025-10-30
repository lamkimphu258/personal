@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name', 'Nutrition Companion'))

@section('body-class', 'bg-slate-950 text-slate-100 min-h-screen')

@section('content')
    <div class="mx-auto w-full max-w-5xl px-4 py-12">
        <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Dashboard</h1>
        <p class="mt-4 text-slate-400">Use the navigation above to manage your profile and future planning tools.</p>
    </div>
@endsection

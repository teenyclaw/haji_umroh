@extends('layouts.app')
@section('title', 'Edit Agent')

@section('content')
    <x-page-header title="Edit Agent" :subtitle="$agent->name" />
    <form method="POST" action="{{ route('app.agents.update', $agent) }}" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        @include('tenant.agents._form')
        <div class="flex justify-end gap-2">
            <a href="{{ route('app.agents.show', $agent) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
            <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan</button>
        </div>
    </form>
@endsection

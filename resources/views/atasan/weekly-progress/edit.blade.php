@extends('layouts.atasan')

@section('title', 'Weekly Progress')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Progress</h1>
    <p class="text-gray-500 mt-1 mb-2">Rabu - {{ now()->translatedFormat('d F Y') }}</p>
    <p class="text-sm text-green-600 mb-8">Sudah disubmit — kamu masih bisa mengubahnya di bawah ini.</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-red-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-500">Target minggu ini</p>
        <p class="font-bold text-gray-900 mt-1">{{ $commitment->big_goal }}</p>
        <p class="text-sm text-gray-600 mt-1">Target: {{ number_format($commitment->target, 0, ',', '.') }}</p>
    </div>

    <form method="POST" action="{{ route('atasan.weekly-progress.update', $progress) }}">
        @csrf
        @method('PUT')

        <label class="block text-sm text-gray-700 font-semibold mb-2">Actual sementara</label>
        <input type="number" step="0.01" name="actual_sementara" required value="{{ old('actual_sementara', $progress->actual_sementara) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-red-400">

        <label class="block text-sm text-gray-700 font-semibold mb-2">Problem / Kendala</label>
        <textarea name="problem" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('problem', $progress->problem) }}</textarea>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Analysis / Penyebab</label>
        <textarea name="analysis" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('analysis', $progress->analysis) }}</textarea>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Solution</label>
        <textarea name="solution" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('solution', $progress->solution) }}</textarea>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Action Plan sampai akhir minggu</label>
        <textarea name="action_plan" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('action_plan', $progress->action_plan) }}</textarea>

        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3.5 rounded-lg transition">
            Update weekly progress
        </button>
    </form>
@endsection
@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10 max-w-2xl">
    <h2 class="text-3xl font-bold mb-3">{{ $job->title }}</h2>
    <p class="text-gray-700 mb-5">{{ $job->description }}</p>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-5">
            {{ session('success') }}
        </div>
    @endif

    @auth('worker')
        <form action="{{ route('jobs.apply', $job->id) }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block font-medium">Cover Letter</label>
                <textarea name="cover_letter" rows="4" class="w-full border rounded p-2"
                          placeholder="Write a short message to the client..."></textarea>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Apply</button>
        </form>
    @else
        <a href="{{ route('register') }}"
           class="bg-blue-500 text-white px-4 py-2 rounded">Sign Up to Apply</a>
    @endauth
</div>
@endsection

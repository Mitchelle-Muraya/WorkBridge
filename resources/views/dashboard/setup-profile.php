@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Complete Your Profile</h2>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Setup Form --}}
    <form action="{{ route('worker.profile.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Skills --}}
        <div class="mb-3">
            <label for="skills" class="form-label">Skills</label>
            <textarea name="skills" id="skills" class="form-control" rows="3">{{ old('skills') }}</textarea>
            @error('skills')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Profile Photo --}}
        <div class="mb-3">
            <label for="photo" class="form-label">Profile Photo</label>
            <input type="file" name="photo" id="photo" class="form-control">
            @error('photo')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h3 class="mb-4 text-center fw-bold text-primary">👷 Complete Your Worker Profile</h3>

                    <form method="POST" action="{{ route('worker.profile.save') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Skills -->
                        <div class="mb-3">
                            <label for="skills" class="form-label fw-semibold">Skills</label>
                            <input type="text" name="skills" id="skills" class="form-control"
                                placeholder="e.g. Plumbing, Carpentry" required>
                        </div>

                        <!-- Experience -->
                        <div class="mb-3">
                            <label for="experience" class="form-label fw-semibold">Experience</label>
                            <input type="text" name="experience" id="experience" class="form-control"
                                placeholder="e.g. 3 years in house maintenance" required>
                        </div>

                        <!-- Photo Upload -->
                        <div class="mb-3">
                            <label for="photo" class="form-label fw-semibold">Upload Photo</label>
                            <input type="file" name="photo" id="photo" class="form-control">
                        </div>

                        <!-- Resume Upload -->
                        <div class="mb-4">
                            <label for="resume" class="form-label fw-semibold">Upload Resume (optional)</label>
                            <input type="file" name="resume" id="resume" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            Save Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

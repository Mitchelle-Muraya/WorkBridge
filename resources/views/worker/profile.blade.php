@extends('layouts.app')

@section('content')
<style>
/* --------- REALISTIC, PRODUCTION-STYLE FORM --------- */
body {
  background: #f4f6f8;
}

.profile-container {
  max-width: 800px;
  margin: 80px auto;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.06);
  overflow: hidden;
}

.profile-header {
  background: linear-gradient(135deg, #00b3ff, #00c9a7);
  padding: 30px;
  text-align: center;
  color: #fff;
}

.profile-header h3 {
  font-weight: 700;
  margin-bottom: 6px;
}

.profile-header p {
  opacity: 0.9;
  font-size: 0.95rem;
}

.profile-form {
  padding: 40px;
}

.form-label {
  font-weight: 600;
  color: #333;
}

.form-control {
  border-radius: 8px;
  padding: 12px;
  border: 1px solid #d1d5db;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #00b3ff;
  box-shadow: 0 0 0 3px rgba(0,179,255,0.2);
}

.btn-save {
  background: linear-gradient(135deg, #00b3ff, #00c9a7);
  border: none;
  border-radius: 8px;
  color: #fff;
  padding: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-save:hover {
  background: linear-gradient(135deg, #00a0e6, #00b89c);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,179,255,0.25);
}

.photo-preview {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-top: 5px;
}

.photo-preview img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #00b3ff;
}
</style>

<div class="profile-container">
  <div class="profile-header">
    <h3> Complete Your Worker Profile</h3>
    <p>Help clients know your skills, experience, and professionalism</p>
  </div>

  <div class="profile-form">
    <form method="POST" action="{{ route('worker.profile.save') }}" enctype="multipart/form-data">
      @csrf

      <!-- Skills -->
      <div class="mb-3">
        <label for="skills" class="form-label">Skills</label>
        <input type="text" name="skills" id="skills" class="form-control"
          placeholder="e.g. Plumbing, Carpentry, Masonry" required>
      </div>

      <!-- Experience -->
      <div class="mb-3">
        <label for="experience" class="form-label">Experience</label>
        <input type="text" name="experience" id="experience" class="form-control"
          placeholder="e.g. 3 years in house maintenance" required>
      </div>

      <!-- Photo Upload -->
      <div class="mb-3">
        <label for="photo" class="form-label">Upload Profile Photo</label>
        <input type="file" name="photo" id="photo" class="form-control">
        <div class="photo-preview">
          <img id="previewImage" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=00b3ff&color=fff" alt="Preview">
          <small class="text-muted">Preview updates automatically</small>
        </div>
      </div>

      <!-- Resume Upload -->
      <div class="mb-4">
        <label for="resume" class="form-label">Upload Resume (optional)</label>
        <input type="file" name="resume" id="resume" class="form-control">
      </div>

      <button type="submit" class="btn-save w-100">Save Profile</button>
    </form>
  </div>
</div>

<script>
// preview uploaded photo
document.getElementById('photo').addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    document.getElementById('previewImage').src = URL.createObjectURL(file);
  }
});
</script>
@endsection

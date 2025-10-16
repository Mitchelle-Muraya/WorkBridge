<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complete Your Profile | WorkBridge</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #0040c1, #00c9a7);
      color: white;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .wizard-container {
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      width: 90%;
      max-width: 700px;
      padding: 40px;
      box-shadow: 0 0 30px rgba(0,0,0,0.25);
      animation: fadeIn 0.7s ease;
    }

    h2 {
      font-weight: 700;
      color: #fff;
      text-align: center;
    }

    .step {
      display: none;
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.4s ease;
    }

    .step.active {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }

    .btn-next, .btn-prev {
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      transition: all 0.3s;
    }

    .btn-next {
      background-color: #00e6b8;
      color: #000;
      font-weight: 600;
    }

    .btn-next:hover {
      background-color: #fff;
      color: #00c9a7;
    }

    .btn-prev {
      background-color: transparent;
      border: 1px solid white;
      color: white;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .progress {
      height: 10px;
      border-radius: 10px;
      margin-bottom: 30px;
    }

    .progress-bar {
      background: #00e6b8;
    }
  </style>
</head>

<body>
  <div class="wizard-container">
    <h2>Let’s complete your profile 👋</h2>
    <p class="text-center mb-4">Just a few quick steps to unlock personalized job matches!</p>

    <!-- Progress Bar -->
    <div class="progress mb-4">
      <div class="progress-bar" id="progressBar" style="width: 25%;"></div>
    </div>

    <form id="onboardingForm" method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data">
      @csrf

      <!-- Step 1 -->
      <div class="step active">
        <h5>Step 1: Personal Info</h5>
        <input type="text" name="phone" class="form-control mb-3" placeholder="Phone Number" required>
        <input type="text" name="location" class="form-control mb-3" placeholder="Location (e.g. Nairobi)" required>
        <button type="button" class="btn-next float-end">Next</button>
      </div>

      <!-- Step 2 -->
      <div class="step">
        <h5>Step 2: Skills & Experience</h5>
        <input type="text" name="skills" class="form-control mb-3" placeholder="Your Skills (e.g. Plumbing, Welding)" required>
        <select name="experience" class="form-control mb-3" required>
          <option value="">Experience Level</option>
          <option value="Less than 1 year">Less than 1 year</option>
          <option value="1-3 years">1-3 years</option>
          <option value="3-5 years">3-5 years</option>
          <option value="5+ years">5+ years</option>
        </select>
        <div class="d-flex justify-content-between">
          <button type="button" class="btn-prev">Back</button>
          <button type="button" class="btn-next">Next</button>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="step">
        <h5>Step 3: Uploads</h5>
        <label>Profile Photo (optional)</label>
        <input type="file" name="photo" class="form-control mb-3" accept="image/*">
        <label>Resume (PDF or DOC)</label>
        <input type="file" name="resume" class="form-control mb-3" accept=".pdf,.doc,.docx">
        <div class="d-flex justify-content-between">
          <button type="button" class="btn-prev">Back</button>
          <button type="button" class="btn-next">Next</button>
        </div>
      </div>

      <!-- Step 4 -->
      <div class="step">
        <h5>Step 4: Review & Submit</h5>
        <p class="text-light">You’re all set! Click below to finish your setup.</p>
        <div class="d-flex justify-content-between">
          <button type="button" class="btn-prev">Back</button>
          <button type="submit" class="btn-submit">Finish Setup 🎉</button>
        </div>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const steps = document.querySelectorAll(".step");
  const nextBtns = document.querySelectorAll(".btn-next");
  const prevBtns = document.querySelectorAll(".btn-prev");
  const progressBar = document.getElementById("progressBar");
  const form = document.getElementById("onboardingForm");

  let currentStep = 0;

  // Function to show the correct step
  function showStep() {
    steps.forEach((step, index) => {
      step.classList.remove("active");
      if (index === currentStep) step.classList.add("active");
    });
    progressBar.style.width = ((currentStep + 1) / steps.length) * 100 + "%";
  }

  // Validation per step
  function validateStep() {
    const inputs = steps[currentStep].querySelectorAll("input[required], select[required]");
    for (let input of inputs) {
      if (!input.value.trim()) {
        input.classList.add("is-invalid");
        setTimeout(() => input.classList.remove("is-invalid"), 1500);
        return false;
      }
    }
    return true;
  }

  // Next buttons
  nextBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      if (btn.type === "submit") return; // skip validation for final submit
      if (!validateStep()) return;
      if (currentStep < steps.length - 1) {
        currentStep++;
        showStep();
      }
    });
  });

  // Previous buttons
  prevBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      if (currentStep > 0) {
        currentStep--;
        showStep();
      }
    });
  });

  // 🎉 Success Animation + Redirect
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Hide the form smoothly
    form.style.opacity = "0";
    form.style.pointerEvents = "none";

    // 🎊 Launch confetti animation
    const duration = 3 * 1000;
    const end = Date.now() + duration;

    (function frame() {
      confetti({
        particleCount: 5,
        spread: 90,
        startVelocity: 45,
        origin: { y: 0.6 }
      });
      if (Date.now() < end) requestAnimationFrame(frame);
    })();

    // 🟢 Show success message
    const successBox = document.createElement("div");
    successBox.innerHTML = `
      <div class="text-center mt-4 p-4 bg-light text-dark rounded-4 shadow-lg">
        <h3 class="fw-bold text-success mb-2">Profile Complete! 🎉</h3>
        <p>You’re all set. Redirecting you to your homepage...</p>
      </div>`;
    form.parentNode.appendChild(successBox);

    // Redirect after short delay
    setTimeout(() => {
      window.location.href = "{{ route('landing') }}";
    }, 4000);
  });

  // Initialize first step
  showStep();
});
</script>

</body>
</html>

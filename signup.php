<?php
$showAlert = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './db/config.php';
    $username = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    $exists = false;
    $checkSql = "SELECT * FROM users WHERE email = '$username'";
    $checkResult = mysqli_query($conn, $checkSql);
    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $exists = true;
    }

    if (!$exists && ($password == $cpassword)) {
        $sql = "INSERT INTO users (email, password) VALUES ('$username', '$password')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $showAlert = true;
        }
    } else {
        $showError = $exists ? "User already exists!" : "Passwords do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup - Water Watch</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          animation: {
            'fade-in-up': 'fadeInUp 0.5s ease-out',
            'float': 'float 6s ease-in-out infinite',
          },
          keyframes: {
            fadeInUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            float: {
              '0%, 100%': { transform: 'translateY(0)' },
              '50%': { transform: 'translateY(-10px)' },
            }
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body class="min-h-screen flex items-center justify-center font-sans relative overflow-hidden">

  <!-- Background Image with Overlay -->
  <div class="absolute inset-0 z-0">
    <img src="https://images.unsplash.com/photo-1541675154750-0444c7d51e8e?q=80&w=2530&auto=format&fit=crop" alt="Water Background" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm"></div>
  </div>

  <!-- Back to Home Button -->
  <a href="index.php" class="absolute top-6 left-6 z-20 flex items-center gap-2 text-white/80 hover:text-white transition-all duration-300 hover:-translate-x-1 group">
    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-blue-500/50 transition">
      <i class="fas fa-arrow-left text-sm"></i>
    </div>
    <span class="font-medium tracking-wide">Back to Home</span>
  </a>

  <!-- Signup Card -->
  <div class="relative z-10 w-full max-w-sm p-6 mx-4 bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl animate-fade-in-up">
    
    <!-- Logo/Icon -->
    <div class="flex justify-center mb-6 animate-float">
      <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-400 flex items-center justify-center shadow-lg shadow-purple-500/30">
        <i class="fa-solid fa-user-plus text-3xl text-white"></i>
      </div>
    </div>

    <h2 class="text-3xl font-bold text-center text-white mb-2">Create Account</h2>
    <p class="text-center text-gray-400 mb-8 text-sm">Join the community to help monitor Delhi's water.</p>

    <?php if ($showAlert): ?>
      <div class="mb-6 flex items-center gap-3 bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-xl text-sm">
        <i class="fas fa-check-circle text-lg"></i>
        <span>Account created successfully!</span>
      </div>
    <?php elseif ($showError): ?>
      <div class="mb-6 flex items-center gap-3 bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm">
        <i class="fas fa-exclamation-circle text-lg"></i>
        <span><?= $showError ?></span>
      </div>
    <?php endif; ?>

    <form action="" method="post" class="space-y-5" onsubmit="return showLoader()">
      <div class="space-y-1">
        <label for="email" class="block text-xs font-medium text-gray-400 uppercase tracking-wider ml-1">Email Address</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="fa-solid fa-envelope text-gray-500"></i>
          </div>
          <input type="text" id="email" name="email" required placeholder="name@example.com"
            class="block w-full pl-11 pr-4 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all duration-300" />
        </div>
      </div>

      <div class="space-y-1">
        <label for="password" class="block text-xs font-medium text-gray-400 uppercase tracking-wider ml-1">Password</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="fa-solid fa-lock text-gray-500"></i>
          </div>
          <input type="password" id="password" name="password" required placeholder="••••••••"
            class="block w-full pl-11 pr-12 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all duration-300" />
          <button type="button" onclick="togglePassword()" class="absolute right-4 top-3.5 text-gray-500 hover:text-gray-300 transition">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <div class="space-y-1">
        <label for="cpassword" class="block text-xs font-medium text-gray-400 uppercase tracking-wider ml-1">Confirm Password</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="fa-solid fa-lock text-gray-500"></i>
          </div>
          <input type="password" id="cpassword" name="cpassword" required placeholder="••••••••"
            class="block w-full pl-11 pr-12 py-3 bg-gray-800/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all duration-300" />
          <button type="button" onclick="toggleCPassword()" class="absolute right-4 top-3.5 text-gray-500 hover:text-gray-300 transition">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <button id="signupBtn" type="submit"
        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-purple-600/30 transform hover:scale-[1.02] transition-all duration-300 flex justify-center items-center gap-2">
        <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"></path>
        </svg>
        <span>Create Account</span>
        <i class="fa-solid fa-arrow-right text-sm"></i>
      </button>

      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-700"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-4 bg-[#1a2230] text-gray-500 rounded-full">Or sign up with</span>
        </div>
      </div>

      <button type="button" onclick="alert('Google Signup not implemented 😅')"
        class="w-full flex items-center justify-center gap-3 bg-white/5 border border-gray-600 text-gray-300 rounded-xl py-3 hover:bg-white/10 transition-all duration-300">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 w-5" alt="Google" />
        <span>Google</span>
      </button>

      <p class="text-center text-gray-400 text-sm mt-6">
        Already have an account?
        <a href="login.php" class="text-purple-400 font-semibold hover:text-purple-300 transition">Sign In</a>
      </p>
    </form>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById("password");
      const icon = event.currentTarget.querySelector('i');
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
    function toggleCPassword() {
      const input = document.getElementById("cpassword");
      const icon = event.currentTarget.querySelector('i');
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    function showLoader() {
      const btn = document.getElementById('signupBtn');
      const spinner = document.getElementById('spinner');
      const text = btn.querySelector('span');
      const icon = btn.querySelector('.fa-arrow-right');
      
      btn.disabled = true;
      btn.classList.add('opacity-75', 'cursor-not-allowed');
      spinner.classList.remove('hidden');
      if(text) text.textContent = 'Creating Account...';
      if(icon) icon.classList.add('hidden');
      
      return true;
    }
  </script>

</body>
</html>

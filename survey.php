<?php 
  session_start();
  if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true){
    header("location: login.php");
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Water Body Survey - Water Watch</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
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
          },
          keyframes: {
            fadeInUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-200 font-sans antialiased relative">

  <!-- Background Image -->
  <div class="fixed inset-0 z-0">
    <img src="https://images.unsplash.com/photo-1541675154750-0444c7d51e8e?q=80&w=2530&auto=format&fit=crop" alt="Water Background" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"></div>
  </div>

  <!-- Navbar -->
  <header class="fixed w-full top-0 z-40 bg-gray-900/80 backdrop-blur-md border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <!-- Logo -->
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='index.php'">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/20">
            <i class="fas fa-droplet text-white text-xl"></i>
          </div>
          <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300 hidden sm:block">Water Watch</h1>
        </div>

        <!-- Desktop Nav -->
        <nav class="hidden sm:flex items-center gap-1">
          <a href="index.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Home</a>
          <a href="index.php#monitoring" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Monitoring</a>
          <a href="predict.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Predict</a>
          <a href="survey.php" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-full transition-all">Survey</a>
          <a href="contact.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Contact</a>
          <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="admin.php" class="px-4 py-2 text-sm font-medium text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400/10 rounded-full transition-all">Admin</a>
          <?php endif; ?>
          
          <div class="ml-4 pl-4 border-l border-gray-700 flex items-center gap-3">
            <span class="text-sm font-medium text-gray-400"><?= htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="text-red-400 hover:text-red-300 transition"><i class="fas fa-sign-out-alt"></i></a>
          </div>
        </nav>
        
        <!-- Mobile Menu Button -->
        <button class="sm:hidden text-gray-400 hover:text-white" onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Mobile Sidebar -->
  <div id="mobileSidebar" class="fixed inset-0 bg-black/90 z-50 hidden sm:hidden flex justify-end">
    <div class="w-64 bg-gray-900 h-full p-6 space-y-6 border-l border-gray-800">
      <div class="flex justify-end">
        <button onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')" class="text-gray-400 hover:text-white">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <nav class="space-y-4">
        <a href="index.php" class="block text-gray-300 hover:text-blue-400 text-lg">Home</a>
        <a href="index.php#monitoring" class="block text-gray-300 hover:text-blue-400 text-lg">Monitoring</a>
        <a href="survey.php" class="block text-white text-lg font-medium">Survey</a>
        <a href="contact.php" class="block text-gray-300 hover:text-blue-400 text-lg">Contact</a>
        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
          <a href="admin.php" class="block text-yellow-400 hover:text-yellow-300 text-lg">Admin</a>
        <?php endif; ?>
        <a href="logout.php" class="block text-red-400 hover:text-red-300 text-lg pt-4 border-t border-gray-800">Logout</a>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <main class="relative z-10 pt-32 pb-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      
      <div class="text-center mb-10 animate-fade-in-up">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Water Body Survey</h2>
        <p class="text-gray-400">Help us track the health of All India's water bodies by submitting your observations.</p>
      </div>

      <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl p-6 sm:p-10 animate-fade-in-up">
        <form action="submit_survey.php" method="POST" class="space-y-8">
          
          <!-- Location -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-blue-400 uppercase tracking-wider"><i class="fas fa-map-marker-alt mr-2"></i>Location</label>
            <select name="location" required class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
              <option value="" class="bg-gray-800">Select a water body...</option>
              <option value="Yamuna River" class="bg-gray-800">Yamuna River</option>
              <option value="Sanjay Lake" class="bg-gray-800">Sanjay Lake</option>
              <option value="Najafgarh Drain" class="bg-gray-800">Najafgarh Drain</option>
            </select>
          </div>

          <!-- Color -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-blue-400 uppercase tracking-wider"><i class="fas fa-eye mr-2"></i>Water Color</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <label class="cursor-pointer">
                <input type="radio" name="color" value="Clear" class="peer sr-only" required>
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-blue-600 peer-checked:border-blue-500 peer-checked:text-white transition hover:bg-gray-700">
                  Clear
                </div>
              </label>
              <label class="cursor-pointer">
                <input type="radio" name="color" value="Greenish" class="peer sr-only">
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-green-600 peer-checked:border-green-500 peer-checked:text-white transition hover:bg-gray-700">
                  Greenish
                </div>
              </label>
              <label class="cursor-pointer">
                <input type="radio" name="color" value="Brownish" class="peer sr-only">
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-amber-700 peer-checked:border-amber-600 peer-checked:text-white transition hover:bg-gray-700">
                  Brownish
                </div>
              </label>
            </div>
          </div>

          <!-- Odor -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-blue-400 uppercase tracking-wider"><i class="fas fa-wind mr-2"></i>Odor Intensity</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <label class="cursor-pointer">
                <input type="radio" name="odor" value="No Odor" class="peer sr-only" required>
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-emerald-600 peer-checked:text-white transition hover:bg-gray-700">
                  None
                </div>
              </label>
              <label class="cursor-pointer">
                <input type="radio" name="odor" value="Mild Odor" class="peer sr-only">
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-yellow-600 peer-checked:text-white transition hover:bg-gray-700">
                  Mild
                </div>
              </label>
              <label class="cursor-pointer">
                <input type="radio" name="odor" value="Strong Odor" class="peer sr-only">
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-red-600 peer-checked:text-white transition hover:bg-gray-700">
                  Strong
                </div>
              </label>
            </div>
          </div>

          <!-- Waste -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-blue-400 uppercase tracking-wider"><i class="fas fa-trash-alt mr-2"></i>Visible Waste</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <?php $wastes = ['Plastic', 'Foam', 'Dead Fish', 'None']; ?>
              <?php foreach($wastes as $waste): ?>
              <label class="cursor-pointer">
                <input type="checkbox" name="waste[]" value="<?= $waste ?>" class="peer sr-only">
                <div class="p-3 rounded-xl border border-gray-600 bg-gray-800/50 text-center text-gray-300 peer-checked:bg-purple-600 peer-checked:border-purple-500 peer-checked:text-white transition hover:bg-gray-700">
                  <?= $waste ?>
                </div>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Comments -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-blue-400 uppercase tracking-wider"><i class="fas fa-comment-alt mr-2"></i>Additional Comments</label>
            <textarea name="comments" rows="4" placeholder="Describe any specific observations..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"></textarea>
          </div>

          <!-- Submit -->
          <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/30 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
            <i class="fas fa-paper-plane"></i> Submit Report
          </button>

        </form>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-950 border-t border-gray-900 py-8 text-center text-gray-500 text-sm relative z-10">
    <p>&copy; 2025 Water Watch. All rights reserved.</p>
  </footer>

</body>
</html>

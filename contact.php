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
  <title>Contact Us - Water Watch</title>
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
          <a href="survey.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Survey</a>
          <a href="contact.php" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-full transition-all">Contact</a>
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
        <a href="survey.php" class="block text-gray-300 hover:text-blue-400 text-lg">Survey</a>
        <a href="contact.php" class="block text-white text-lg font-medium">Contact</a>
        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
          <a href="admin.php" class="block text-yellow-400 hover:text-yellow-300 text-lg">Admin</a>
        <?php endif; ?>
        <a href="logout.php" class="block text-red-400 hover:text-red-300 text-lg pt-4 border-t border-gray-800">Logout</a>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <main class="relative z-10 pt-32 pb-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      
      <div class="text-center mb-16 animate-fade-in-up">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Get in Touch</h2>
        <p class="text-gray-400 max-w-2xl mx-auto">We'd love to hear from you. Send us a message or reach out to our team directly.</p>
      </div>

      <div class="grid lg:grid-cols-2 gap-12 items-start">
        
        <!-- Contact Form -->
        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl p-8 animate-fade-in-up">
          <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="fas fa-envelope-open-text text-blue-400"></i> Send a Message
          </h3>
          <form id="contactForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-medium text-gray-400">Name</label>
                <input type="text" name="name" required class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-medium text-gray-400">Email</label>
                <input type="email" name="email" required class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-400">Subject</label>
              <input type="text" name="subject" required class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-400">Message</label>
              <textarea name="message" rows="5" required class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/30 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
              <i class="fas fa-paper-plane"></i> Send Message
            </button>
          </form>
        </div>

        <!-- Team Section -->
        <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s;">
          <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="fas fa-users text-blue-400"></i> Our Team
          </h3>
          
          <div class="grid sm:grid-cols-2 gap-6">
            <!-- Member 1 -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:bg-gray-800 transition-all duration-300 group text-center">
              <div class="relative w-24 h-24 mx-auto mb-4">
                <div class="absolute inset-0 bg-blue-500 rounded-full blur opacity-20 group-hover:opacity-40 transition"></div>
                <img src="https://media.licdn.com/dms/image/v2/D5603AQFqFBfgi-xN0g/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1707564979338?e=1766016000&v=beta&t=tBpBtcYKMZ4p_HtFDC1s5HN4kiRYLHSzagfZXmOFX9s" alt="Ayushman" class="relative w-full h-full rounded-full object-cover border-2 border-blue-500">
              </div>
              <h4 class="text-lg font-bold text-white mb-1">Ayushman Bhattacharya</h4>
              <p class="text-blue-400 text-sm mb-3">Lead Developer</p>
              <div class="flex justify-center gap-3 text-gray-400">
                <a href="https://www.linkedin.com/in/ayushman30/" class="hover:text-white transition"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
            <!-- Member 2 -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 hover:bg-gray-800 transition-all duration-300 group text-center">
              <div class="relative w-24 h-24 mx-auto mb-4">
                <div class="absolute inset-0 bg-gray-500 rounded-full blur opacity-20 group-hover:opacity-40 transition"></div>
                <img src="https://media.licdn.com/dms/image/v2/D5603AQHD3CDEXuJrag/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1712597624902?e=1766016000&v=beta&t=_OSL0ppo_diJwXDMl4z5l8xn7m6dlVmxsxcgpP5yhKY" alt="Aryan" class="relative w-full h-full rounded-full object-cover border-2 border-gray-500">
              </div>
              <h4 class="text-lg  font-bold text-white mb-1">Aryan Singh</h4>
              <p class="text-red-500 text-sm mb-3">ML Engineer</p>
              <div class="flex justify-center gap-3 text-gray-400">
                <a href="https://www.linkedin.com/in/singh-aryan-as09/" class="hover:text-white transition"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>

  </main>

  <!-- Footer -->
  <footer class="bg-gray-950 border-t border-gray-900 py-8 text-center text-gray-500 text-sm relative z-10">
    <p>&copy; 2025 Water Watch. All rights reserved.</p>
  </footer>

  <script>
    // Contact Form Submission
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);

      try {
        const response = await fetch('submit_contact.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.text();
        
        if (response.ok) {
          alert(result); // Or show a nice modal
          this.reset();
        } else {
          throw new Error(result);
        }
      } catch (error) {
        alert(error.message || 'Error sending message. Please try again later.');
        console.error('Error:', error);
      }
    });
  </script>
</body>
</html>

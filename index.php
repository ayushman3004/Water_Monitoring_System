<?php 
  session_start();
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Water Monitoring - Delhi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            primary: '#4f46e5',
            secondary: '#9333ea',
            accent: '#06b6d4',
          },
          animation: {
            'fade-in': 'fadeIn 1s ease-out',
            'slide-up': 'slideUp 0.8s ease-out',
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: '0' },
              '100%': { opacity: '1' },
            },
            slideUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-200 font-sans antialiased selection:bg-blue-500 selection:text-white">

<!-- Mobile Sidebar -->
<div id="mobileSidebar" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden transition-opacity duration-300">
  <div class="absolute left-0 top-0 w-72 h-full bg-gray-900 border-r border-gray-800 p-6 space-y-6 shadow-2xl transform transition-transform duration-300">
    <div class="flex justify-between items-center border-b border-gray-800 pb-4">
      <h2 class="text-xl font-bold text-blue-400">Menu</h2>
      <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white transition">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    
    <nav class="space-y-4">
      <a href="index.php" class="flex items-center gap-3 text-gray-300 hover:text-blue-400 hover:bg-gray-800 px-4 py-3 rounded-xl transition-all">
        <i class="fas fa-home w-6"></i> Home
      </a>
      
      <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a href="#monitoring" class="flex items-center gap-3 text-gray-300 hover:text-blue-400 hover:bg-gray-800 px-4 py-3 rounded-xl transition-all">
          <i class="fas fa-chart-line w-6"></i> Monitoring
        </a>
        <a href="survey.php" class="flex items-center gap-3 text-gray-300 hover:text-blue-400 hover:bg-gray-800 px-4 py-3 rounded-xl transition-all">
          <i class="fas fa-clipboard-list w-6"></i> Survey
        </a>
        <a href="contact.php" class="flex items-center gap-3 text-gray-300 hover:text-blue-400 hover:bg-gray-800 px-4 py-3 rounded-xl transition-all">
          <i class="fas fa-envelope w-6"></i> Contact
        </a>
        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
          <a href="admin.php" class="flex items-center gap-3 text-yellow-400 hover:text-yellow-300 hover:bg-gray-800 px-4 py-3 rounded-xl transition-all">
            <i class="fas fa-shield-alt w-6"></i> Admin
          </a>
        <?php endif; ?>
        
        <div class="pt-6 border-t border-gray-800 mt-6">
          <div class="flex items-center gap-3 px-4 mb-4">
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
              <?= strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($_SESSION['username']); ?></p>
              <p class="text-xs text-gray-500 truncate">Online</p>
            </div>
          </div>
          <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 hover:bg-red-500/10 px-4 py-3 rounded-xl transition-all">
            <i class="fas fa-sign-out-alt w-6"></i> Logout
          </a>
        </div>
      <?php else: ?>
        <div class="pt-6 border-t border-gray-800 mt-6 space-y-3">
          <a href="login.php" class="block w-full text-center bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded-xl font-medium transition-all shadow-lg shadow-blue-900/20">Login</a>
          <a href="signup.php" class="block w-full text-center bg-gray-800 hover:bg-gray-700 text-white px-4 py-3 rounded-xl font-medium transition-all border border-gray-700">Signup</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</div>

<!-- Navbar -->
<header class="fixed w-full top-0 z-40 transition-all duration-300 bg-gray-900/80 backdrop-blur-md border-b border-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-20">
      <!-- Logo -->
      <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.location.href='index.php'">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/20">
          <i class="fas fa-droplet text-white text-xl"></i>
        </div>
        <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300 tracking-tight">Water Watch</h1>
      </div>
      
      <!-- Hamburger -->
      <button class="sm:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition" onclick="toggleSidebar()">
        <i class="fas fa-bars text-xl"></i>
      </button>

      <!-- Desktop Nav -->
      <nav class="hidden sm:flex items-center gap-1">
        <a href="index.php" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-full transition-all">Home</a>
        
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
          <a href="index.php#monitoring" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Monitoring</a>
          <a href="predict.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Predict</a>
          <a href="survey.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Survey</a>
          <a href="contact.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Contact</a>
          <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="admin.php" class="px-4 py-2 text-sm font-medium text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400/10 rounded-full transition-all">Admin</a>
          <?php endif; ?>

          <!-- User Menu -->
          <div class="relative ml-4 group">
            <button class="flex items-center gap-2 pl-4 pr-2 py-1.5 bg-gray-800 hover:bg-gray-700 rounded-full border border-gray-700 transition-all">
              <span class="text-sm font-medium text-gray-200"><?= htmlspecialchars($_SESSION['username']); ?></span>
              <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-xs font-bold text-white">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)); ?>
              </div>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right z-50">
              <div class="py-1">
                <a href="logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-400 hover:bg-gray-700/50 transition">
                  <i class="fas fa-sign-out-alt"></i> Logout
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="flex items-center gap-3 ml-4">
            <a href="login.php" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 rounded-full border border-gray-700 transition-all">Login</a>
            <a href="signup.php" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-full shadow-lg shadow-blue-600/20 transition-all">Signup</a>
          </div>
        <?php endif; ?>
      </nav>
    </div>
  </div>
</header>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
  <!-- Background Image -->
  <div class="absolute inset-0 z-0">
    <img src="https://images.unsplash.com/photo-1541675154750-0444c7d51e8e?q=80&w=2530&auto=format&fit=crop" alt="Yamuna River" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/90 via-gray-900/70 to-gray-900"></div>
  </div>

  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-medium mb-8 animate-fade-in">
      <span class="relative flex h-2 w-2">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
      </span>
      Live Water Quality Monitoring
    </div>
    
    <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-6 animate-slide-up">
      Protecting Delhi's <br/>
      <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Lifeline Together</span>
    </h1>
    
    <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto mb-10 leading-relaxed animate-slide-up" style="animation-delay: 0.1s;">
      Empowering citizens with real-time data and community-driven surveys to restore and preserve our water bodies.
    </p>
    
    <div class="flex flex-col sm:flex-row justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s;">
      <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a href="#monitoring" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-full font-semibold shadow-lg shadow-blue-600/30 transition-all transform hover:scale-105 flex items-center justify-center gap-2">
          <i class="fas fa-chart-bar"></i> View Data
        </a>
        <a href="survey.php" class="px-8 py-4 bg-gray-800/80 hover:bg-gray-700 backdrop-blur-sm text-white border border-gray-700 rounded-full font-semibold transition-all transform hover:scale-105 flex items-center justify-center gap-2">
          <i class="fas fa-pen-to-square"></i> Submit Survey
        </a>
      <?php else: ?>
        <a href="login.php" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-full font-semibold shadow-lg shadow-blue-600/30 transition-all transform hover:scale-105">
          Get Started
        </a>
        <a href="#about" class="px-8 py-4 bg-gray-800/80 hover:bg-gray-700 backdrop-blur-sm text-white border border-gray-700 rounded-full font-semibold transition-all transform hover:scale-105">
          Learn More
        </a>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Scroll Indicator -->
  <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce text-gray-400">
    <i class="fas fa-chevron-down text-2xl"></i>
  </div>
</section>

<!-- Monitoring Section -->
<section id="monitoring" class="py-24 bg-gray-900 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
      <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Real-Time Water Quality</h2>
      <p class="text-gray-400 max-w-2xl mx-auto">Live data feed from monitoring stations across Delhi.</p>
    </div>
    
    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
      <?php
        $csvFile = fopen(__DIR__ . "/WBC_DL_0.csv", "r");
        if ($csvFile !== FALSE) {
          $headers = fgetcsv($csvFile);
          $excludeColumns = ["ward_name", "town_municipality_name"];
          $includeIndexes = [];

          foreach ($headers as $index => $header) {
            if (!in_array(trim(strtolower($header)), $excludeColumns)) {
              $includeIndexes[] = $index;
            }
          }

          $includeIndexes = array_slice($includeIndexes, 0, 10);
          $maxRows = 6;
          $rowCount = 0;

          echo '<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">';
          
          while (($row = fgetcsv($csvFile)) !== FALSE && $rowCount < $maxRows) {
            echo '<div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-6 hover:bg-gray-800 transition-all duration-300 group">';
            echo '<div class="flex items-center gap-3 mb-4 border-b border-gray-700 pb-3">';
            echo '<div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-colors"><i class="fas fa-water"></i></div>';
            echo '<h3 class="font-semibold text-white">Station #'.($rowCount + 1).'</h3>';
            echo '</div>';
            
            echo '<div class="space-y-3">';
            $displayCount = 0;
            foreach ($includeIndexes as $index) {
              if($displayCount >= 4) break; // Limit details per card
              $label = isset($headers[$index]) ? htmlspecialchars($headers[$index]) : "Param";
              $value = isset($row[$index]) ? htmlspecialchars($row[$index]) : "N/A";
              
              echo '<div class="flex justify-between items-center text-sm">';
              echo '<span class="text-gray-400">'.$label.'</span>';
              echo '<span class="font-medium text-gray-200">'.$value.'</span>';
              echo '</div>';
              $displayCount++;
            }
            echo '</div>';
            echo '</div>';
            $rowCount++;
          }
          echo '</div>';

          fclose($csvFile);
        } else {
          echo "<div class='text-center p-10 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400'>Error: Unable to read data source.</div>";
        }
      ?>
    <?php else: ?>
      <div class="relative rounded-3xl overflow-hidden bg-gray-800 border border-gray-700 p-12 text-center">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
        <div class="relative z-10">
          <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-lock text-3xl text-gray-400"></i>
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">Access Restricted</h3>
          <p class="text-gray-400 mb-8 max-w-md mx-auto">Please login to view detailed water quality reports and real-time monitoring data.</p>
          <div class="flex justify-center gap-4">
            <a href="login.php" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-full font-medium transition-all">Login</a>
            <a href="signup.php" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-full font-medium transition-all">Create Account</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-24 bg-gray-900 border-t border-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-16 items-center">
      <div class="relative group">
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
        <img src="https://static.wixstatic.com/media/6ade0b_d6b74eb90c21457fa89682d573ad8a2f~mv2.jpg" alt="About Project" class="relative rounded-2xl shadow-2xl w-full object-cover h-[400px] transform transition duration-500 group-hover:scale-[1.01]">
      </div>
      
      <div class="space-y-8">
        <div>
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">About The Initiative</h2>
          <div class="w-20 h-1 bg-blue-500 rounded-full"></div>
        </div>
        
        <div class="space-y-6 text-gray-400 leading-relaxed">
          <p>
            <strong class="text-white">Water Watch</strong> is a citizen-focused initiative combining technology and community engagement to monitor the health of All India's water bodies.
          </p>
          <p>
            By integrating government data with crowd-sourced insights, we provide a transparent platform for tracking key parameters like <span class="text-blue-400">pH, TDS, DO, and BOD</span>.
          </p>
        </div>
        
        <ul class="space-y-4">
          <li class="flex items-start gap-3">
            <div class="mt-1 w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 flex-shrink-0">
              <i class="fas fa-check text-xs"></i>
            </div>
            <span class="text-gray-300">Real-time data visualization</span>
          </li>
          <li class="flex items-start gap-3">
            <div class="mt-1 w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 flex-shrink-0">
              <i class="fas fa-check text-xs"></i>
            </div>
            <span class="text-gray-300">Community-driven surveys</span>
          </li>
          <li class="flex items-start gap-3">
            <div class="mt-1 w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 flex-shrink-0">
              <i class="fas fa-check text-xs"></i>
            </div>
            <span class="text-gray-300">Open source & transparent</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-gray-950 border-t border-gray-900 pt-16 pb-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid md:grid-cols-4 gap-12 mb-12">
      <div class="col-span-1 md:col-span-2 space-y-4">
        <div class="flex items-center gap-2">
          <i class="fas fa-droplet text-blue-500 text-xl"></i>
          <h3 class="text-xl font-bold text-white">Water Watch</h3>
        </div>
        <p class="text-gray-400 max-w-sm">Empowering citizens with environmental data transparency for a cleaner, safer future.</p>
      </div>
      
      <div>
        <h4 class="text-white font-semibold mb-6">Quick Links</h4>
        <ul class="space-y-3 text-sm text-gray-400">
          <li><a href="index.php" class="hover:text-blue-400 transition">Home</a></li>
          <li><a href="#monitoring" class="hover:text-blue-400 transition">Monitoring</a></li>
          <li><a href="survey.php" class="hover:text-blue-400 transition">Survey</a></li>
          <li><a href="contact.php" class="hover:text-blue-400 transition">Contact</a></li>
        </ul>
      </div>
      
      <div>
        <h4 class="text-white font-semibold mb-6">Contact</h4>
        <ul class="space-y-3 text-sm text-gray-400">
          <li class="flex items-center gap-2"><i class="fas fa-envelope text-gray-600"></i> support@waterwatch.in</li>
          <li class="flex items-center gap-2"><i class="fas fa-phone text-gray-600"></i> +91 8167394620</li>
          <li class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-gray-600"></i> Delhi, India</li>
        </ul>
      </div>
    </div>
    
    <div class="border-t border-gray-900 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
      <p class="text-gray-500 text-sm">© 2025 Water Watch. All rights reserved.</p>
      <div class="flex gap-4">
        <a href="#" class="text-gray-500 hover:text-white transition"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-gray-500 hover:text-white transition"><i class="fab fa-github"></i></a>
        <a href="#" class="text-gray-500 hover:text-white transition"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>
  </div>
</footer>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("mobileSidebar");
    sidebar.classList.toggle("hidden");
  }
</script>
</body>
</html>

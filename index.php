<?php 
  session_start();
  if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true){
    header("location: login.php");
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Water Monitoring - Delhi</title>
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
          colors: {
            primary: '#4f46e5',
            secondary: '#9333ea',
            accent: '#06b6d4',
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-200 font-sans">

<!-- Navbar -->
<header class="bg-gray-800 shadow-md">
  <div class="max-w-6xl mx-auto p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-blue-300">💧 Delhi Water Watch</h1>
    <button id="menuToggle" class="sm:hidden text-2xl text-blue-300 focus:outline-none">☰</button>
    <nav id="navbar" class="hidden sm:flex flex-col sm:flex-row sm:space-x-4 sm:items-center w-full sm:w-auto mt-4 sm:mt-0">
      <a href="index.php" class="hover:text-blue-400 font-medium transition block sm:inline">Home</a>
      <a href="#monitoring" class="hover:text-blue-400 font-medium transition block sm:inline">Monitoring</a>
      <a href="survey.php" class="hover:text-blue-400 text-blue-300 transition block sm:inline">Survey</a>
      <a href="admin.php" class="hover:text-blue-400 font-medium transition block sm:inline" onclick="return checkAdminAccess()">Admin</a>

      <!-- User Menu -->
      <div class="relative inline-block text-left block sm:inline" onclick="document.getElementById('userMenu').classList.toggle('hidden')">
        <button class="inline-flex justify-center items-center w-full rounded-full px-4 py-2 bg-gray-700 text-md font-medium text-gray-200 hover:bg-gray-600 transition">
          <i class="fa-solid fa-user"></i>
        </button>
        <div id="userMenu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-gray-700 ring-1 ring-black ring-opacity-5 hidden z-50">
          <div class="py-1">
            <a href="logout.php" class="block px-4 py-2 text-sm text-red-400 hover:bg-red-800 transition">Logout</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
</header>

<!-- Hero Section -->
<section class="relative text-center py-32 bg-cover bg-center bg-no-repeat" style="background-image: url('https://www.crossroadadventure.com/wp-content/uploads/2024/01/Yamuna-Ghat_7-scaled.jpg')">
  <div class="absolute inset-0 bg-gray-900 opacity-80"></div>
  <div class="relative z-10 max-w-5xl mx-auto px-4 text-gray-100">
    <h2 class="text-4xl font-extrabold mb-4">Real-Time Water Monitoring & Survey</h2>
    <p class="text-lg max-w-2xl mx-auto">Empowering Delhi to track and improve the quality of water bodies through real-time data and community input.</p>
    <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
      <a href="#monitoring" class="bg-primary text-white px-6 py-2 rounded-full shadow hover:bg-indigo-700 transition">View Monitoring</a>
      <a href="survey.php" class="bg-secondary text-white px-6 py-2 rounded-full shadow hover:bg-purple-700 transition">Submit a Survey</a>
    </div>
  </div>
</section>

<!-- Monitoring Section -->
<section id="monitoring" class="py-16 px-4 bg-gray-900">
  <div class="max-w-6xl mx-auto">
    <h3 class="text-3xl font-bold text-white mb-10 text-center">📊 Water Quality Dashboard</h3>
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
      $maxRows = 5;
      $rowCount = 0;

      while (($row = fgetcsv($csvFile)) !== FALSE && $rowCount < $maxRows) {
        echo '<div class="mb-8 p-4 rounded-xl border border-gray-600 bg-gray-800 shadow">';
        echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">';
        foreach ($includeIndexes as $index) {
          $label = isset($headers[$index]) ? htmlspecialchars($headers[$index]) : "Column $index";
          $value = isset($row[$index]) ? htmlspecialchars($row[$index]) : "N/A";
          echo "
            <div class='p-3 bg-gray-700 rounded-2xl shadow-md hover:bg-gray-600 transition-all duration-300 transform hover:scale-105'>
              <h4 class='text-xs text-gray-400 font-medium truncate'>{$label}</h4>
              <p class='text-base font-bold text-blue-400 break-words'>{$value}</p>
            </div>
          ";
        }
        echo '</div></div>';
        $rowCount++;
      }

      fclose($csvFile);
    } else {
      echo "<p class='text-red-400'>Error: Unable to read the CSV file.</p>";
    }
  ?>
  </div>
</section>

<!-- About Section -->
<section class="max-w-6xl mx-auto py-16 px-4">
  <h3 class="text-3xl font-bold text-white mb-10 text-center">📌 About the Project</h3>
  <div class="grid md:grid-cols-2 gap-10 items-center">
    <img src="https://static.wixstatic.com/media/6ade0b_d6b74eb90c21457fa89682d573ad8a2f~mv2.jpg" alt="River in Delhi" class="rounded-xl shadow-md w-full h-full max-h-[400px] object-cover">
    <div class="text-gray-300 space-y-5">
      <p><strong>Delhi Water Watch</strong> is a citizen-focused initiative that combines technology, environmental awareness, and data transparency to monitor the health of Delhi's water bodies in real time.</p>
      <p>By integrating government CSV (e.g., data.gov.in), we provide visual dashboards of key water quality parameters like <strong>pH, TDS, DO, BOD</strong>. The platform also collects crowd-sourced insights from the public through a user-friendly survey system.</p>
      <p>This project empowers residents to engage with water safety data, encouraging collaborative efforts toward environmental conservation and better policy implementation.</p>
      <ul class="list-disc pl-5">
        <li><strong>Tech Stack:</strong> HTML, Tailwind CSS, PHP, JavaScript, MySQL</li>
        <li><strong>Live Monitoring:</strong> Based on real-time CSV/API feeds</li>
        <li><strong>Open Data:</strong> Supports public + government datasets</li>
      </ul>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 border-t py-5 text-center text-sm text-gray-400">
  Developed with ❤️ by Ayushman Bhattacharya | Contact: <a href="mailto:ayushman@example.com" class="text-blue-400 hover:underline">ayushman@example.com</a>
</footer>

<script>
  function checkAdminAccess() {
    const isAdmin = <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 'true' : 'false'; ?>;
    if (!isAdmin) {
      alert("⚠️ Only admins can access this page.");
      return false;
    }
    return true;
  }
</script>
</body>
</html>

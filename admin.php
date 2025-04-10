<?php


// Allow only admins
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit;
}





require './db/config.php';

$sql = "SELECT * FROM survey_responses ORDER BY submitted_at ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - Survey Data</title>
  <script src="https://cdn.tailwindcss.com"></script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-thumb {
      background-color: #cbd5e0;
      border-radius: 6px;
    }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans">
<!-- navbar -->
<div id="mobileSidebar" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden sm:hidden">
  <div class="absolute left-0 top-0 w-64 h-full bg-gray-800 p-6 space-y-4 shadow-lg transform transition-transform duration-300 translate-x-0">
    <button onclick="toggleSidebar()" class="text-white text-xl absolute top-4 right-4">
      <i class="fas fa-times"></i>
    </button>
    <a href="index.php" class="block text-white text-lg hover:text-blue-400">Home</a>
    <a href="index.php #monitoring" class="block text-white text-lg hover:text-blue-400">Monitoring</a>
    <a href="survey.php" class="block text-white text-lg hover:text-blue-400">Survey</a>
    <a href="contact.php" class="block text-white text-lg hover:text-blue-400">Contact</a>
    <a href="admin.php" class="block text-white text-lg hover:text-blue-400" onclick="return checkAdminAccess()">Admin</a>
    <a href="logout.php" class="block text-red-400 mt-4 hover:text-red-500">Logout</a>
  </div>
</div>
<!-- Navbar -->
<header class="bg-gray-800 shadow-md">
  <div class="max-w-6xl mx-auto p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-blue-300">💧 Delhi Water Watch</h1>
    
    <!-- Hamburger for Mobile -->
    <button id="menuToggle" class="sm:hidden text-2xl text-blue-300 focus:outline-none" onclick="toggleSidebar()">☰</button>

    <!-- Desktop Navbar -->
    <nav id="navbar" class="hidden sm:flex flex-col sm:flex-row sm:space-x-4 sm:items-center w-full sm:w-auto mt-4 sm:mt-0">
      <a href="index.php" class="hover:text-blue-400 font-medium transition block sm:inline">Home</a>
      <a href="index.php #monitoring" class="hover:text-blue-400 font-medium transition block sm:inline">Monitoring</a>
      <a href="survey.php" class="hover:text-blue-400 text-blue-300 transition block sm:inline">Survey</a>
      <a href="contact.php" class="text-white font-medium transition block sm:inline">Contact</a>
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

<!-- Main Content -->
<main class="max-w-7xl mx-auto p-6 sm:p-10">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <h2 class="text-3xl font-semibold mb-4 sm:mb-0">📋 Submitted Survey Records</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Records: <span class="font-semibold"><?php echo $result->num_rows; ?></span></p>
  </div>

  <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-blue-600 text-white text-sm">
  <tr>
    <th class="px-4 py-3 text-left">ID</th>
    <th class="px-4 py-3 text-left">Username</th>
    <th class="px-4 py-3 text-left">Location</th>
    <th class="px-4 py-3 text-left">Color</th>
    <th class="px-4 py-3 text-left">Odor</th>
    <th class="px-4 py-3 text-left">Waste</th>
    <th class="px-4 py-3 text-left">Comments</th>
    <th class="px-4 py-3 text-left">Submitted At</th>
  </tr>
</thead>

      <tbody class="text-sm divide-y divide-gray-200 dark:divide-gray-700">
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-4 py-3"><?php echo $row['id']; ?></td>
              <td class="px-4 py-3"><?php echo htmlspecialchars($row['username']); ?></td>
              <td class="px-4 py-3"><?php echo htmlspecialchars($row['location']); ?></td>
              <td class="px-4 py-3"><?php echo htmlspecialchars($row['color']); ?></td>
              <td class="px-4 py-3"><?php echo htmlspecialchars($row['odor']); ?></td>
              <td class="px-4 py-3"><?php echo htmlspecialchars($row['waste']); ?></td>
              <td class="px-4 py-3"><?php echo htmlspecialchars($row['comments']); ?></td>
              <td class="px-4 py-3 whitespace-nowrap"><?php echo $row['submitted_at']; ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">No survey submissions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
   function toggleSidebar() {
    const sidebar = document.getElementById("mobileSidebar");
    sidebar.classList.toggle("hidden");
  }
</script>
</body>
</html>

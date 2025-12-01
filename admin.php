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

$contact_sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$contact_result = $conn->query($contact_sql);
?>

<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - Water Watch</title>
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
          <a href="contact.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Contact</a>
          <a href="admin.php" class="px-4 py-2 text-sm font-medium text-yellow-400 bg-yellow-400/10 rounded-full transition-all">Admin</a>
          
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
        <a href="contact.php" class="block text-gray-300 hover:text-blue-400 text-lg">Contact</a>
        <a href="admin.php" class="block text-yellow-400 hover:text-yellow-300 text-lg">Admin</a>
        <a href="logout.php" class="block text-red-400 hover:text-red-300 text-lg pt-4 border-t border-gray-800">Logout</a>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <main class="relative z-10 pt-32 pb-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-12">
      
      <!-- Survey Records Section -->
      <div class="animate-fade-in-up">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
          <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-clipboard-list text-blue-400"></i> Survey Records
          </h2>
          <div class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-full text-blue-400 text-sm font-medium">
            Total Records: <span class="text-white ml-1"><?php echo $result->num_rows; ?></span>
          </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
              <thead class="bg-gray-800/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Location</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Color</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Odor</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Waste</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Comments</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800">
                <?php if ($result && $result->num_rows > 0): ?>
                  <?php while($row = $result->fetch_assoc()): ?>  
                    <tr class="hover:bg-gray-800/50 transition-colors">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $row['id']; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white"><?php echo htmlspecialchars($row['username']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-300"><?php echo htmlspecialchars($row['location']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($row['color']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($row['odor']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($row['waste']); ?></td>
                      <td class="px-6 py-4 text-sm text-gray-400 max-w-xs truncate"><?php echo htmlspecialchars($row['comments']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No survey submissions found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Contact Messages Section -->
      <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
          <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-envelope-open-text text-purple-400"></i> Contact Messages
          </h2>
          <div class="px-4 py-2 bg-purple-500/10 border border-purple-500/20 rounded-full text-purple-400 text-sm font-medium">
            Total Messages: <span class="text-white ml-1"><?php echo $contact_result ? $contact_result->num_rows : 0; ?></span>
          </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
              <thead class="bg-gray-800/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Subject</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Message</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800">
                <?php if ($contact_result && $contact_result->num_rows > 0): ?>
                  <?php while($row = $contact_result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-800/50 transition-colors">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $row['id']; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white"><?php echo htmlspecialchars($row['name']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($row['email']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-300"><?php echo htmlspecialchars($row['subject']); ?></td>
                      <td class="px-6 py-4 text-sm text-gray-400 max-w-xs truncate"><?php echo htmlspecialchars($row['message']); ?></td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($row['is_read']): ?>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                            Read
                          </span>
                        <?php else: ?>
                          <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                              Unread
                            </span>
                            <button onclick="markAsRead(<?php echo $row['id']; ?>)" class="text-xs text-blue-400 hover:text-blue-300 hover:underline">
                              Mark Read
                            </button>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No contact messages found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
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
    async function markAsRead(messageId) {
      try {
        const formData = new FormData();
        formData.append('message_id', messageId);

        const response = await fetch('update_message_status.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.text();
        
        if (response.ok) {
          location.reload();
        } else {
          alert('Error: ' + result);
        }
      } catch (error) {
        alert('Error updating message status');
        console.error('Error:', error);
      }
    }
  </script>
</body>
</html>

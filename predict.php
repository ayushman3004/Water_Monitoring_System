<?php 
  session_start();
  if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true){
    header("location: login.php");
    exit;
  }

  $prediction_result = null;
  $image_result = null;
  $error_message = null;
  $active_tab = isset($_POST['tab']) ? $_POST['tab'] : 'params';

  // Parameter-based prediction
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['predict_params'])) {
      $active_tab = 'params';
      $ph = floatval($_POST['ph']);
      $hardness = floatval($_POST['hardness']);
      $turbidity = floatval($_POST['turbidity']);
      $sulfate = floatval($_POST['sulfate']);
      $chlorine = floatval($_POST['chlorine']);

      // Call FastAPI endpoint
      $api_url = "https://fast-api-pani-1.onrender.com/predict";
      $data = [
          "pH" => $ph,
          "Hardness" => $hardness,
          "Turbidity" => $turbidity,
          "Sulfate" => $sulfate,
          "Chlorine" => $chlorine
      ];
      
      $ch = curl_init($api_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_error = curl_error($ch);
      curl_close($ch);
      
      if ($response && $http_code === 200) {
          $prediction_result = json_decode($response, true);
          if (isset($prediction_result['error'])) {
              $error_message = $prediction_result['error'];
              $prediction_result = null;
          }
      } else {
          if ($curl_error) {
              $error_message = "API connection failed: " . $curl_error . ". Make sure FastAPI is running on port 8000.";
          } else {
              $error_message = "API error (HTTP $http_code). Response: " . substr($response, 0, 200);
          }
      }
  }

  // Image-based prediction
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['predict_image'])) {
      $active_tab = 'image';
      
      if (isset($_FILES['water_image']) && $_FILES['water_image']['error'] === UPLOAD_ERR_OK) {
          $file = $_FILES['water_image'];
          $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
          
          if (!in_array($file['type'], $allowed_types)) {
              $error_message = "Invalid file type. Please upload JPG or PNG image.";
          } else {
              // Call FastAPI image endpoint
              $api_url = "https://fast-api-pani-1.onrender.com/predict/image";
              
              $cfile = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
              
              $ch = curl_init($api_url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $cfile]);
              curl_setopt($ch, CURLOPT_TIMEOUT, 30);
              curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
              
              $response = curl_exec($ch);
              $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
              $curl_error = curl_error($ch);
              curl_close($ch);
              
              if ($response && $http_code === 200) {
                  $image_result = json_decode($response, true);
                  if (isset($image_result['error'])) {
                      $error_message = $image_result['error'];
                      $image_result = null;
                  }
              } else {
                  if ($curl_error) {
                      $error_message = "API connection failed: " . $curl_error;
                  } else {
                      $error_message = "Image analysis failed (HTTP $http_code). Response: " . substr($response, 0, 200);
                  }
              }
          }
      } else {
          $error_message = "Please select an image file to upload.";
      }
  }
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Predict Quality - Water Watch</title>
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

  <!-- Toast Notification -->
  <div id="toast" class="fixed top-24 right-5 z-50 transform transition-all duration-300 translate-x-full opacity-0">
    <div class="bg-gray-800 border border-blue-500/30 text-blue-400 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 backdrop-blur-md">
      <div class="relative flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
      </div>
      <span id="toast-message" class="font-medium">Processing...</span>
    </div>
  </div>

  <!-- Background Image -->
  <div class="fixed inset-0 z-0">
    <img src="https://images.unsplash.com/photo-1541675154750-0444c7d51e8e?q=80&w=2530&auto=format&fit=crop" alt="Water Background" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"></div>
  </div>

  <!-- Navbar -->
  <header class="fixed w-full top-0 z-40 bg-gray-900/80 backdrop-blur-md border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='index.php'">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/20">
            <i class="fas fa-droplet text-white text-xl"></i>
          </div>
          <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300 hidden sm:block">Water Watch</h1>
        </div>

        <nav class="hidden sm:flex items-center gap-1">
          <a href="index.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Home</a>
          <a href="index.php#monitoring" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Monitoring</a>
          <a href="predict.php" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-full transition-all">Predict</a>
          <a href="survey.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Survey</a>
          <a href="contact.php" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-full transition-all">Contact</a>
          <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="admin.php" class="px-4 py-2 text-sm font-medium text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400/10 rounded-full transition-all">Admin</a>
          <?php endif; ?>
          
          <div class="ml-4 pl-4 border-l border-gray-700 flex items-center gap-3">
            <span class="text-sm font-medium text-gray-400"><?= htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="text-red-400 hover:text-red-300 transition"><i class="fas fa-sign-out-alt"></i></a>
          </div>
        </nav>
        
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
        <a href="predict.php" class="block text-white text-lg font-medium">Predict</a>
        <a href="survey.php" class="block text-gray-300 hover:text-blue-400 text-lg">Survey</a>
        <a href="contact.php" class="block text-gray-300 hover:text-blue-400 text-lg">Contact</a>
        <a href="logout.php" class="block text-red-400 hover:text-red-300 text-lg pt-4 border-t border-gray-800">Logout</a>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <main class="relative z-10 pt-32 pb-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      
      <div class="text-center mb-8 animate-fade-in-up">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Water Quality Prediction</h2>
        <p class="text-gray-400 max-w-2xl mx-auto">Analyze water quality using parameters or upload an image for pollution detection.</p>
      </div>

      <!-- Tabs -->
      <div class="flex justify-center mb-8">
        <div class="bg-gray-800/50 p-1 rounded-xl inline-flex">
          <button onclick="switchTab('params')" id="tab-params" class="px-6 py-3 rounded-lg text-sm font-medium transition-all <?= $active_tab === 'params' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' ?>">
            <i class="fas fa-sliders-h mr-2"></i>Parameters
          </button>
          <button onclick="switchTab('image')" id="tab-image" class="px-6 py-3 rounded-lg text-sm font-medium transition-all <?= $active_tab === 'image' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' ?>">
            <i class="fas fa-camera mr-2"></i>Image Upload
          </button>
        </div>
      </div>

      <?php if ($error_message): ?>
        <div class="mb-8 bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in-up">
          <i class="fas fa-exclamation-circle text-xl"></i>
          <div>
            <h4 class="font-bold">Error</h4>
            <p class="text-sm"><?= htmlspecialchars($error_message) ?></p>
          </div>
        </div>
      <?php endif; ?>

      <!-- Parameter-based Prediction Tab -->
      <div id="panel-params" class="<?= $active_tab === 'params' ? '' : 'hidden' ?>">
        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl p-8 animate-fade-in-up">
          
          <?php if ($prediction_result): ?>
            <div class="mb-8 <?= $prediction_result['is_potable'] ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-red-500/10 border-red-500/20 text-red-400' ?> border p-6 rounded-xl text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full <?= $prediction_result['is_potable'] ? 'bg-green-500/20' : 'bg-red-500/20' ?> mb-4">
                <i class="fas <?= $prediction_result['is_potable'] ? 'fa-check' : 'fa-times' ?> text-3xl"></i>
              </div>
              <h3 class="text-2xl font-bold mb-2"><?= $prediction_result['prediction_text'] ?></h3>
              <p class="text-gray-300 mb-4">
                Confidence: <span class="font-bold"><?= number_format($prediction_result['confidence'] * 100, 1) ?>%</span> | 
                Quality Score: <span class="font-bold"><?= number_format($prediction_result['quality_score'], 1) ?>/100</span>
              </p>
              <div class="w-full bg-gray-700 rounded-full h-2.5 mb-2 max-w-md mx-auto">
                <div class="bg-current h-2.5 rounded-full" style="width: <?= $prediction_result['quality_score'] ?>%"></div>
              </div>
              <button onclick="window.location.href='predict.php'" class="mt-4 px-6 py-2 bg-gray-800 hover:bg-gray-700 rounded-full text-sm font-medium transition">Analyze Another Sample</button>
            </div>
          <?php else: ?>

          <form action="" method="post" class="space-y-8" onsubmit="showToast('Analysing parameters...')">
            <input type="hidden" name="tab" value="params">
            <div class="grid md:grid-cols-2 gap-8">
              
              <!-- pH Level -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-400">pH Level (0 - 14)</label>
                <div class="relative">
                  <input type="number" step="0.1" min="0" max="14" name="ph" required value="7.0" 
                    class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                  <div class="absolute right-4 top-3.5 text-gray-500 text-sm">pH</div>
                </div>
                <p class="text-xs text-gray-500">Ideal range: 6.5 - 8.5</p>
              </div>

              <!-- Hardness -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-400">Hardness</label>
                <div class="relative">
                  <input type="number" step="0.1" name="hardness" required value="150.0"
                    class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                  <div class="absolute right-4 top-3.5 text-gray-500 text-sm">mg/L</div>
                </div>
                <p class="text-xs text-gray-500">Ideal: ≤ 200 mg/L</p>
              </div>

              <!-- Turbidity -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-400">Turbidity</label>
                <div class="relative">
                  <input type="number" step="0.1" name="turbidity" required value="4.0"
                    class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                  <div class="absolute right-4 top-3.5 text-gray-500 text-sm">NTU</div>
                </div>
                <p class="text-xs text-gray-500">Ideal: ≤ 5 NTU</p>
              </div>

              <!-- Sulfate -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-400">Sulfate</label>
                <div class="relative">
                  <input type="number" step="0.1" name="sulfate" required value="200.0"
                    class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                  <div class="absolute right-4 top-3.5 text-gray-500 text-sm">mg/L</div>
                </div>
                <p class="text-xs text-gray-500">Ideal: ≤ 250 mg/L</p>
              </div>

              <!-- Chlorine -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-400">Chlorine</label>
                <div class="relative">
                  <input type="number" step="0.1" name="chlorine" required value="1.0"
                    class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                  <div class="absolute right-4 top-3.5 text-gray-500 text-sm">mg/L</div>
                </div>
                <p class="text-xs text-gray-500">Ideal: 0.2 - 2.0 mg/L</p>
              </div>

            </div>

            <div class="pt-4">
              <button type="submit" name="predict_params" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/30 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fas fa-microscope"></i> Analyze Water Quality
              </button>
            </div>
          </form>
          <?php endif; ?>

        </div>
      </div>

      <!-- Image-based Prediction Tab -->
      <div id="panel-image" class="<?= $active_tab === 'image' ? '' : 'hidden' ?>">
        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 rounded-2xl shadow-2xl p-8 animate-fade-in-up">
          
          <?php if ($image_result): ?>
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-20 h-20 rounded-full <?= $image_result['is_polluted'] ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400' ?> mb-6">
                <i class="fas <?= $image_result['is_polluted'] ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> text-4xl"></i>
              </div>
              
              <h3 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($image_result['label']) ?></h3>
              
              <div class="mb-6">
                <p class="text-gray-400 mb-2">Pollution Level</p>
                <div class="w-full bg-gray-700 rounded-full h-4 max-w-md mx-auto overflow-hidden">
                  <div class="h-4 rounded-full transition-all duration-500 <?= $image_result['pollution_percentage'] >= 75 ? 'bg-red-500' : ($image_result['pollution_percentage'] >= 50 ? 'bg-yellow-500' : 'bg-green-500') ?>" 
                       style="width: <?= $image_result['pollution_percentage'] ?>%"></div>
                </div>
                <p class="text-2xl font-bold mt-2 <?= $image_result['is_polluted'] ? 'text-red-400' : 'text-green-400' ?>">
                  <?= number_format($image_result['pollution_percentage'], 1) ?>%
                </p>
              </div>
              
              <div class="p-4 rounded-xl <?= $image_result['is_polluted'] ? 'bg-red-500/10 border border-red-500/20' : 'bg-green-500/10 border border-green-500/20' ?> max-w-md mx-auto mb-6">
                <?php if ($image_result['pollution_percentage'] >= 75): ?>
                  <p class="text-red-400"><i class="fas fa-exclamation-circle mr-2"></i>High pollution detected! Water treatment strongly recommended.</p>
                <?php elseif ($image_result['pollution_percentage'] >= 50): ?>
                  <p class="text-yellow-400"><i class="fas fa-exclamation-triangle mr-2"></i>Moderate pollution detected. Further testing recommended.</p>
                <?php else: ?>
                  <p class="text-green-400"><i class="fas fa-check-circle mr-2"></i>Water appears relatively clean.</p>
                <?php endif; ?>
              </div>
              
              <button onclick="window.location.href='predict.php?tab=image'" class="px-6 py-2 bg-gray-800 hover:bg-gray-700 rounded-full text-sm font-medium transition">
                Analyze Another Image
              </button>
            </div>
          <?php else: ?>

          <form action="" method="post" enctype="multipart/form-data" class="space-y-8" onsubmit="showToast('Image is analysing...')">
            <input type="hidden" name="tab" value="image">
            
            <div class="text-center">
              <div class="mb-6">
                <i class="fas fa-cloud-upload-alt text-6xl text-blue-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-white mb-2">Upload Water Image</h3>
                <p class="text-gray-400 text-sm">Upload a photo of water to analyze pollution levels based on color characteristics.</p>
              </div>
              
              <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 hover:border-blue-500 transition-colors cursor-pointer" onclick="document.getElementById('water_image').click()">
                <input type="file" id="water_image" name="water_image" accept="image/jpeg,image/jpg,image/png" class="hidden" onchange="previewImage(this)">
                <div id="upload-placeholder">
                  <i class="fas fa-image text-4xl text-gray-500 mb-3"></i>
                  <p class="text-gray-400">Click to select or drag and drop</p>
                  <p class="text-gray-500 text-sm mt-1">JPG, JPEG, PNG (Max 10MB)</p>
                </div>
                <div id="image-preview" class="hidden">
                  <img id="preview-img" src="" alt="Preview" class="max-h-64 mx-auto rounded-lg mb-3">
                  <p id="file-name" class="text-gray-400 text-sm"></p>
                </div>
              </div>
              
              <div class="mt-6 p-4 bg-gray-800/50 rounded-xl text-left">
                <h4 class="font-medium text-white mb-2"><i class="fas fa-info-circle text-blue-400 mr-2"></i>Detection Capabilities</h4>
                <ul class="text-sm text-gray-400 space-y-1">
                  <li>• <span class="text-blue-400">Clean Water</span> - Clear, blue-tinted water</li>
                  <!-- <li>• <span class="text-green-400">Algae Presence</span> - Green-tinted water</li> -->
                  <li>• <span class="text-red-400">Pollution</span> - Red/brown-tinted water</li>
                </ul>
              </div>
            </div>

            <div class="pt-4">
              <button type="submit" name="predict_image" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-purple-600/30 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fas fa-search"></i> Analyze Image
              </button>
            </div>
          </form>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-950 border-t border-gray-900 py-8 text-center text-gray-500 text-sm relative z-10">
    <p>&copy; 2025 Water Watch. All rights reserved.</p>
  </footer>

  <script>
    function switchTab(tab) {
      // Update tab buttons
      document.getElementById('tab-params').classList.remove('bg-blue-600', 'text-white');
      document.getElementById('tab-params').classList.add('text-gray-400');
      document.getElementById('tab-image').classList.remove('bg-blue-600', 'text-white');
      document.getElementById('tab-image').classList.add('text-gray-400');
      
      document.getElementById('tab-' + tab).classList.remove('text-gray-400');
      document.getElementById('tab-' + tab).classList.add('bg-blue-600', 'text-white');
      
      // Update panels
      document.getElementById('panel-params').classList.add('hidden');
      document.getElementById('panel-image').classList.add('hidden');
      document.getElementById('panel-' + tab).classList.remove('hidden');
    }
    
    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('preview-img').src = e.target.result;
          document.getElementById('file-name').textContent = input.files[0].name;
          document.getElementById('upload-placeholder').classList.add('hidden');
          document.getElementById('image-preview').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
    
    // Set active tab from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'image') {
      switchTab('image');
    }
    function showToast(message) {
      const toast = document.getElementById('toast');
      const toastMessage = document.getElementById('toast-message');
      
      toastMessage.textContent = message;
      toast.classList.remove('translate-x-full', 'opacity-0');
    }

  </script>

</body>
</html>

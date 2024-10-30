<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stalker Instagram - Pandiiy</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-brands/css/uicons-brands.css'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

        *,
        body,
        html {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }

        input {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }
    </style>
    <script>
        function showLoading(button) {
            button.innerHTML = 'Loading';
            button.disabled = true;

            let dots = 0;
            const loadingInterval = setInterval(() => {
                dots = (dots + 1) % 4;
                const dotString = '.'.repeat(dots);
                button.innerHTML = 'Loading' + dotString;
            }, 500);

            button.form.onsubmit = () => {
                clearInterval(loadingInterval);
            };
        }
    </script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen" style="background: linear-gradient(to bottom, #0061ff, #60efff);">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg w-full max-w-xs md:max-w-md flex-grow mt-20 mb-20">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Stalker Jirr</h1>
        <form method="POST" action="" class="mb-4" onsubmit="showLoading(this.querySelector('button'));">
            <input type="text" name="username" placeholder="masukkan username akun" required
                class="w-full p-3 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <span class="tutorial block mb-4 text-gray-600">Noted: Pastikan akun tidak di Private agar jumlah followers terlihat.</span>
            <button type="submit"
                class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-200">Cek Akun</button>
        </form>
        <div id="result">
            <?php
            session_start();

            // Fungsi untuk mengambil nilai dari file .env
            function getEnvValue($key)
            {
                $env = parse_ini_file('.env');
                return isset($env[$key]) ? $env[$key] : null;
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $username = htmlspecialchars($_POST['username']);
                $apiKey = getEnvValue('API_KEY'); // Ambil API key dari .env
                $url = 'https://api.velixs.com/instagram';

                $data = array(
                    'apikey' => $apiKey,
                    'username' => $username
                );

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    $_SESSION['error'] = "Terjadi kesalahan: " . curl_error($ch);
                } else {
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($httpCode == 200) {
                        $response = json_decode($result, true);
                        if (isset($response['status']) && $response['status'] == 1) {
                            $_SESSION['data'] = $response['data'];
                        } else {
                            $_SESSION['error'] = "Terjadi kesalahan: " . htmlspecialchars($response['message']);
                        }
                    } else {
                        $_SESSION['error'] = "Terjadi kesalahan. HTTP Code: $httpCode";
                    }
                }
                curl_close($ch);

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            if (isset($_SESSION['data'])) {
                $data = $_SESSION['data'];
                echo "<div class='bg-gray-100 p-4 rounded-lg shadow-sm mt-4'>";
                echo "<h2 class='text-xl font-semibold mb-2'>Hasil Pencarian:</h2>";
                echo "<p><strong>Nama:</strong> " . (isset($data['name']) ? htmlspecialchars($data['name']) : 'Tidak tersedia') . "</p>";
                echo "<p><strong>Username:</strong> " . (isset($data['username']) ? htmlspecialchars($data['username']) : 'Tidak tersedia') . "</p>";
                echo "<p><strong>Pengikut:</strong> " . (isset($data['followers']) ? htmlspecialchars($data['followers']) : 'Tidak tersedia') . "</p>";
                echo "<p><strong>Mengikuti:</strong> " . (isset($data['following']) ? htmlspecialchars($data['following']) : 'Tidak tersedia') . "</p>";
                echo "<p><strong>Postingan:</strong> " . (isset($data['posts']) ? htmlspecialchars($data['posts']) : 'Tidak tersedia') . "</p>";
                if (isset($data['image'])) {
                    echo "<p><strong>Foto Profil:</strong></p><img src=\"" . htmlspecialchars($data['image']) . "\" alt=\"Foto Profil\" class='w-24 h-24 rounded-full mx-auto mt-2 shadow-md'>";
                }
                echo "</div>";
                unset($_SESSION['data']);
            }

            if (isset($_SESSION['error'])) {
                echo "<p class='text-red-600 font-semibold'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            }
            ?>
        </div>
    </div>

    <footer class="bg-gray-800 text-white p-4 mt-10 text-center w-full">
        <p class="text-sm mb-2">© 2024 Pandiiy. All rights reserved.</p>
        <div>
            <a href="https://www.instagram.com/affandiiangrhh._" target="_blank" class="mx-2">
                <i class="fi fi-brands-instagram"></i>
            </a>
            <a href="https://github.com/affndiangrh" target="_blank" class="mx-2">
                <i class="fi fi-brands-github"></i>
            </a>
        </div>
    </footer>
</body>



</html>
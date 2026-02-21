<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-[#0b1117] text-white flex items-center justify-center min-h-screen p-4">
    <div class="glass p-10 rounded-3xl shadow-2xl max-w-lg w-full text-center">
        <h1 class="text-6xl font-black mb-6 text-gray-800 opacity-50 uppercase tracking-widest">500</h1>
        <h2 class="text-3xl font-bold mb-4">Something Went Wrong</h2>
        <p class="text-gray-400 mb-8 lidering-relaxed">
            We have been notified about this issue and we are working to fix it. 
            Please try again after some time.
        </p>
        <a href="/" class="inline-block py-3 px-8 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
            Back to Home
        </a>
    </div>
</body>
</html>

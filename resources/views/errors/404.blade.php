<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found · COMSOC Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-between">

    <div class="flex-1 flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-20 h-20 rounded-3xl bg-comsoc-50 border border-comsoc-200 flex items-center justify-center shadow-sm">
                    <img src="{{ asset('images/comsoc_logo.png') }}" alt="COMSOC" class="w-12 h-12 object-contain"/>
                </div>
            </div>

            <div>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-black tracking-widest uppercase bg-comsoc-50 text-comsoc-700 border border-comsoc-200">
                    HTTP 404 · Not Found
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mt-3">Page Not Found</h1>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    The requested page or record could not be found or may have been removed.
                </p>
            </div>

            <div class="flex items-center justify-center gap-3 pt-2">
                <button onclick="window.history.back()"
                        class="px-5 py-2.5 bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 text-sm font-bold rounded-xl transition shadow-xs">
                    ← Go Back
                </button>
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 bg-comsoc-600 hover:bg-comsoc-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-200/60 bg-white">
        COMSOC Hub · Computer Society · CCIS · University of Makati
    </footer>

</body>
</html>

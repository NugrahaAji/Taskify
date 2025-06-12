<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="src/style.css" rel="stylesheet">
</head>
<body class="bg-black h-screen flex items-center justify-center font-sans">
  <div class="flex w-full max-w-6xl h-[80vh] rounded-3xl overflow-hidden shadow-2xl">

    <!-- Left (Black background with logo) -->
    <div class="w-1/2 bg-black flex items-start justify-start p-8">
      <h1 class="text-white text-2xl font-bold tracking-wide">TASKI<span class="text-white relative">FY<sup class="text-xs">°</sup></span></h1>
    </div>

    <!-- Right (Login form) -->
    <div class="w-1/2 bg-white p-16 relative">
      <h2 class="text-5xl font-semibold text-black mb-10">Login</h2>

      <form class="space-y-6">
        <div class="grid grid-cols-2 gap-6">
          <!-- Email -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Email</label>
            <input type="email" class="w-full border-b border-black outline-none focus:ring-0" require spellcheck="false" />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Password</label>
            <input type="password" class="w-full border-b border-black outline-none focus:ring-0" require spellcheck="false" />
          </div>
        </div>

        <!-- Remember Me and Create Account -->
        <div class="flex justify-between text-sm text-gray-600 italic pt-2">
          <label class="flex items-center space-x-2">
            <input type="checkbox" class="form-checkbox" />
            <span>Remember me?</span>
          </label>
          <a href="#" class="hover:underline">Create an account?</a>
        </div>

        <!-- Submit button -->
        <div class="absolute bottom-10 right-10">
          <button type="submit" class="bg-black text-white rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-800 ">
            <span class="text-xl">→</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

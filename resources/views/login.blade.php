<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center min-h-screen" style="background-color: cadetblue">
    <div class="max-w-lg w-11/12 bg-white border-2 border-white/20 backdrop-blur-md shadow-md rounded-lg p-8 mx-auto mt-12">
        <h2 id="loginTitle" class="text-3xl text-center font-bold mb-5">Login</h2>

        @if (session('error'))
            <p style="color: red;">{{ session('error') }}</p>
        @endif

        @if (session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif

        <!-- Login Form -->
        <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <input type="email" placeholder="Enter email" name="email" value="{{ old('email') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('email') ? 'border-red-500' : '' }}">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="password" placeholder="Password" name="password"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('password') ? 'border-red-500' : '' }}">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full h-12 bg-black text-white font-bold rounded-full mt-5 hover:bg-gray-900 transition">Login</button>
            <p class="text-center text-sm mt-4">
                Don't have an account?
                <a href="{{ url('/register') }}" class="text-black font-semibold hover:underline">Register</a>
            </p>
            <p class="text-center text-sm mt-2">
                <a href="#" onclick="showResetForm()" class="text-black-500 font-semibold hover:underline">Forgot Password?</a>
            </p>
        </form>

        <!-- Reset Password Form (hidden by default) -->
        <div id="resetPasswordForm" class="hidden">
            <h2 class="text-2xl text-center font-bold mb-3">Reset Password</h2>
            <form action="{{ route('reset.password') }}" method="POST">
                @csrf
                <input type="email" placeholder="Enter email" name="email"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none mb-3" required>

                <input type="password" placeholder="New Password" name="new_password"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none mb-3" required>

                <input type="password" placeholder="Confirm Password" name="new_password_confirmation"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none mb-3" required>

                <button type="submit" class="w-full h-12 bg-black text-white font-bold rounded-full mt-5 hover:bg-gray-900 transition">
                    Change Password
                </button>
            </form>
            <p class="text-center text-sm mt-2">
                <a href="#" onclick="showLoginForm()" class="text-red-500 font-semibold hover:underline">Back to Login</a>
            </p>
        </div>
    </div>

    <!-- JavaScript for toggling forms -->
    <script>
        function showResetForm() {
            document.getElementById("loginForm").classList.add("hidden");
            document.getElementById("resetPasswordForm").classList.remove("hidden");
            document.getElementById("loginTitle").style.display = "none";
        }

        function showLoginForm() {
            document.getElementById("resetPasswordForm").classList.add("hidden");
            document.getElementById("loginForm").classList.remove("hidden");
            document.getElementById("loginTitle").style.display = "block";
        }

        setTimeout(() => {
        const messages = document.querySelectorAll('p[style]');
        messages.forEach(msg => {
            msg.style.transition = 'opacity 1s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 1000); // Remove message completely after fade
        });
    }, 3000);
    </script>
</body>
</html>
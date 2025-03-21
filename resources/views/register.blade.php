<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex justify-center items-center min-h-screen" style="background-color: cadetblue">
    <div class="max-w-lg w-11/12 bg-white border-2 border-white/20 backdrop-blur-md shadow-md rounded-lg p-8 mx-auto mt-12">
        <h2 class="text-3xl text-center font-bold mb-5">Register</h2>
        <form action="/register" method="post" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <input type="text" placeholder="First name" name="fname" value="{{ old('fname') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('fname') ? 'border-red-500' : '' }}">
                @error('fname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="Last name" name="lname" value="{{ old('lname') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('lname') ? 'border-red-500' : '' }}">
                @error('lname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="email" placeholder="Enter email" name="email" value="{{ old('email') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('email') ? 'border-red-500' : '' }}">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Password Field with Show/Hide Feature -->
                <div class="relative">
                    <input type="password" id="password" placeholder="Password" name="password"
                        class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none pr-10 {{ $errors->has('password') ? 'border-red-500' : '' }}">
                    <span onclick="togglePassword()" class="absolute right-3 top-3 cursor-pointer text-gray-600">
                        👁
                    </span>
                </div>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="Phone" name="phone" value="{{ old('phone') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('phone') ? 'border-red-500' : '' }}">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="About" name="about" value="{{ old('about') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('about') ? 'border-red-500' : '' }}">
                @error('about') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="Enter address" name="address" value="{{ old('address') }}"
                    class="w-full h-10 border border-gray-400 rounded-md px-3 text-black outline-none {{ $errors->has('address') ? 'border-red-500' : '' }}">
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="file" name="avatar" class="w-full border border-gray-400 rounded-md p-2">
                @error('avatar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
                class="w-full h-12 bg-black text-white font-bold rounded-full mt-5 hover:bg-gray-900 transition">
                Register
            </button>
        </form>
    </div>

    <!-- JavaScript for Show/Hide Password -->
    <script>
        function togglePassword() {
            let passwordInput = document.getElementById("password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
</body>

</html>

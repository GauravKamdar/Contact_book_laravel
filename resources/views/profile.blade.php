@extends('layout')

@section('title', 'Profile Page')
@section('header-title', 'Profile Page')

@section('content')
    <!-- Edit Profile Form (Now Always Visible) -->
    <div class="w-full max-w-lg mx-auto bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-white text-center text-2xl font-semibold mb-4">Edit Profile</h2>

        <!-- Avatar Upload Section -->
        <div class="flex justify-center mb-4 relative">
            <!-- Avatar Container -->
            <div id="avatarContainer" class="relative cursor-pointer">
                <div id="avatarPreview">
                    @if($user->avatar)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" 
                            class="w-28 h-28 rounded-full object-cover border-4 border-blue-500 shadow-lg mx-auto" title="✏change">
                    @else
                        <div class="w-28 h-28 flex items-center justify-center 
                            bg-blue-500 text-white font-bold rounded-full text-3xl shadow-lg mx-auto">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hidden File Input for Avatar Upload -->
            <input type="file" name="avatar" id="avatarInput" class="hidden">

            <!-- Avatar Options Menu -->
            <div id="avatarOptions" class="absolute top-full mt-2 left-1/2 transform -translate-x-1/2 bg-gray-800 p-2 rounded-lg shadow-md hidden">
                <label class="block text-white cursor-pointer hover:bg-gray-700 px-4 py-2 rounded" for="avatarInput">Upload New</label>
                <button id="removeAvatarBtn" class="block text-red-400 hover:bg-gray-700 px-4 py-2 rounded">Remove</button>
            </div>
        </div>

        <!-- Profile Edit Form -->
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="text-white block mb-1">First Name</label>
                <input type="text" name="first_name" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $user->first_name }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $user->last_name }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Email</label>
                <input type="email" name="email" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $user->email }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Phone</label>
                <input type="text" name="phone" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $user->phone }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Address</label>
                <input type="text" name="address" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $user->address }}">
            </div>

            <!-- Save & Cancel Buttons -->
            <div class="flex justify-between mt-6">
                <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition">Save</button>
                <a href="{{ url('/home') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 transition">Cancel</a>
            </div>
        </form>   
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const avatarContainer = document.getElementById("avatarContainer");
        const avatarOptions = document.getElementById("avatarOptions");
        const avatarInput = document.getElementById("avatarInput");
        const avatarPreview = document.getElementById("avatarPreview");
        const removeAvatarBtn = document.getElementById("removeAvatarBtn");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        const userInitial = "{{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}";

        // Toggle Avatar Options Menu
        avatarContainer.addEventListener("click", function (event) {
            event.stopPropagation();
            avatarOptions.classList.toggle("hidden");
        });

        // Hide Avatar Options When Clicking Outside
        document.addEventListener("click", function (event) {
            if (!avatarContainer.contains(event.target) && !avatarOptions.contains(event.target)) {
                avatarOptions.classList.add("hidden");
            }
        });

        // Handle Avatar Upload
        avatarInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append("avatar", file);
            formData.append("_token", csrfToken);

            // Show image preview immediately
            const reader = new FileReader();
            reader.onload = function (e) {
                avatarPreview.innerHTML = `<img src="${e.target.result}" class="w-28 h-28 rounded-full object-cover border-4 border-blue-500 shadow-lg mx-auto">`;
            };
            reader.readAsDataURL(file);

            // Upload image via AJAX
            fetch("{{ route('profile.uploadAvatar') }}", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    avatarPreview.innerHTML = `<img src="${data.avatar_url}" class="w-28 h-28 rounded-full object-cover border-4 border-blue-500 shadow-lg mx-auto">`;
                    alert("Avatar uploaded successfully!");
                }
            })
            .catch(error => console.error("Error uploading avatar:", error));
        });

        // Remove Avatar
        removeAvatarBtn.addEventListener("click", function () {
            if (!confirm("Are you sure you want to remove your avatar?")) return;

            // Update UI Immediately
            avatarPreview.innerHTML = `<div class="w-28 h-28 flex items-center justify-center bg-blue-500 text-white font-bold rounded-full text-3xl shadow-lg mx-auto">${userInitial}</div>`;

            fetch("{{ route('profile.removeAvatar') }}", {
                method: "DELETE",
                headers: { 
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) alert("Avatar removed successfully!");
            })
            .catch(error => console.error("Error removing avatar:", error));
        });
    });
</script>
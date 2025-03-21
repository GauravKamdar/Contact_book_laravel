@extends('layout')

@section('title', 'My Contacts')
@section('header-title', 'My Contacts')

@section('content')
    <div class="w-full max-w-lg mx-auto bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-white text-center text-2xl font-semibold mb-4">Edit Contact</h2>

        <!-- Avatar Upload Section -->
        <div class="flex justify-center mb-4 relative">
            <div id="avatarContainer" class="relative cursor-pointer">
                <div id="avatarPreview">
                    @if($contact->avatar_url)
                        <img src="{{ asset('storage/avatars/' . $contact->avatar_url) }}" 
                            class="w-28 h-28 rounded-full object-cover border-4 border-blue-500 shadow-lg mx-auto" title="✏change">
                    @else
                        <div class="w-28 h-28 flex items-center justify-center 
                            bg-blue-500 text-white font-bold rounded-full text-3xl shadow-lg mx-auto">
                            {{ strtoupper(substr($contact->first_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
            <input type="file" name="avatar" id="avatarInput" class="hidden">
            <div id="avatarOptions" class="absolute top-full mt-2 left-1/2 transform -translate-x-1/2 bg-gray-800 p-2 rounded-lg shadow-md hidden">
                <label class="block text-white cursor-pointer hover:bg-gray-700 px-4 py-2 rounded" for="avatarInput">Upload New</label>
                <button id="removeAvatarBtn" class="block text-red-400 hover:bg-gray-700 px-4 py-2 rounded">Remove</button>
            </div>
        </div>

        <!-- Contact Edit Form -->
        <form method="POST" action="{{ url('/update-contact/' . $contact->id) }}">
            @csrf

            <div class="mb-4">
                <label class="text-white block mb-1">First Name</label>
                <input type="text" name="fname" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $contact->first_name }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Last Name</label>
                <input type="text" name="lname" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $contact->last_name }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Email</label>
                <input type="email" name="email" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $contact->email }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Phone</label>
                <input type="text" name="phone" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $contact->phone }}" required>
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">WhatsApp Number</label>
                <input type="text" name="wpnumber" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $contact->whatsapp_number }}">
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">Address</label>
                <input type="text" name="address" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500" value="{{ $contact->address }}">
            </div>

            <div class="mb-4">
                <label class="text-white block mb-1">About</label>
                <textarea name="about" class="w-full p-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500">{{ $contact->about }}</textarea>
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition">Save</button>
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
        const contactId = "{{ $contact->id }}";
        const contactInitial = "{{ strtoupper(substr($contact->first_name, 0, 1)) }}";

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
            formData.append("avatar_url", file);
            formData.append("_token", csrfToken);

            // Show image preview immediately
            const reader = new FileReader();
            reader.onload = function (e) {
                avatarPreview.innerHTML = `<img src="${e.target.result}" class="w-28 h-28 rounded-full object-cover border-4 border-blue-500 shadow-lg mx-auto">`;
            };
            reader.readAsDataURL(file);

            // Upload image via AJAX
            fetch(`/contacts/${contactId}/upload-avatar`, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
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
            avatarPreview.innerHTML = `<div class="w-28 h-28 flex items-center justify-center bg-blue-500 text-white font-bold rounded-full text-3xl shadow-lg mx-auto">${contactInitial}</div>`;

            fetch(`/contacts/${contactId}/remove-avatar`, {
                method: "DELETE",
                headers: { 
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) return;
            })
            .catch(error => console.error("Error removing avatar:", error));
        });
    });
</script>
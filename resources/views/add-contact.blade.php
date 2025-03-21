@extends('layout')

@section('title', 'Add Contacts')
@section('header-title', 'Add Contact')

@section('content')
    <div class="max-w-lg w-full bg-gray-800 border border-gray-700 shadow-md rounded-lg p-8 mx-auto mt-10">
        <h2 class="text-3xl text-center font-bold mb-5">Add Contact</h2>
        <form action="{{ route('addcontact.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="space-y-3">
                <input type="text" placeholder="First name" name="fname" value="{{ old('fname') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('fname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="Last name" name="lname" value="{{ old('lname') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('lname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="email" placeholder="Enter email" name="email" value="{{ old('email') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="Phone" name="phone" value="{{ old('phone') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="About" name="about" value="{{ old('about') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('about') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="Enter address" name="address" value="{{ old('address') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="text" placeholder="WhatsApp number" name="wpnumber" value="{{ old('wpnumber') }}"
                    class="w-full h-10 border border-gray-600 rounded-md px-3 bg-gray-700 text-white outline-none">
                @error('wpnumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <input type="file" name="avatar" class="w-full border border-gray-600 rounded-md p-2 bg-gray-700 text-white">
                @error('avatar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
                class="w-full h-12 bg-blue-500 text-white font-bold rounded-md mt-5 hover:bg-blue-600 transition">
                Create Contact
            </button>
        </form>
    </div>
@endsection
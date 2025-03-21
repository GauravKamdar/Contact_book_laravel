@extends('layout')
@section('title', 'My Contacts')
@section('header-title', 'My Contacts')
@section('extra-search')
    <div class="flex justify-between items-center mb-4">
        <!-- Add Contact Button -->
        <a href="{{ url('/home') }}" class="py-2 px-4 rounded hover:bg-gray-700" title="back to home">
            ← Back
        </a>
    </div>
@endsection

@section('content')
    <!-- Contacts Table -->
    <div class="overflow-auto mt-4">
        <table class="w-full bg-gray-800 text-white border border-gray-700 rounded-lg">
            <thead class="bg-gray-700">
                <tr>
                    <th class="p-3">Fav</th>
                    <th class="p-3">Avatar</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr class="border-b border-gray-700">
                    <td class="p-3 text-center">
                        <button onclick="toggleFavorite({{ $contact->id }}, this)"
                            class="favorite-btn text-xl"
                            title="{{ $contact->is_favorite ? 'Remove from Favorites' : 'Mark as Favorite' }}">
                            {{ $contact->is_favorite ? '⭐' : '☆' }}
                        </button>
                    </td>
                    <td class="p-3 text-center">
                        <img src="{{ $contact->avatar_url ? asset('storage/avatars/' . $contact->avatar_url) : 'https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png' }}" 
                            alt="Avatar" class="w-10 h-10 rounded-full mx-auto">
                    </td>
                        <td class="p-3 text-center">{{ $contact->first_name }} {{ $contact->last_name }}</td>
                        <td class="p-3 text-center">{{ $contact->email }}</td>
                        <td class="p-3 text-center">{{ $contact->phone }}</td>
                        <td class="p-3 flex gap-2 justify-center h-[55px]">
                        <a href="{{ url('/edit-contact/' . $contact->id) }}"
                               class="bg-yellow-500 px-3 py-1 rounded hover:bg-yellow-600 transition">
                                Edit
                            </a>
                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 transition"
                                        onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($contacts->hasPages())
        <div class="flex justify-between mt-4">
            <div>
                @if (!$contacts->onFirstPage())
                    <a href="{{ $contacts->previousPageUrl() }}"
                       class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 transition">
                        Previous
                    </a>
                @endif
            </div>
            <div>
                @if ($contacts->hasMorePages())
                    <a href="{{ $contacts->nextPageUrl() }}"
                       class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 transition">
                        Next
                    </a>
                @endif
            </div>
        </div>
    @endif
<script>
function toggleFavorite(contactId) {
    fetch(`/toggle-favorite/${contactId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
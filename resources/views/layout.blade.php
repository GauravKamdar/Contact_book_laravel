<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">

<!-- Sidebar -->
<aside class="w-64 bg-gray-800 h-screen fixed top-0 left-0 z-50 p-5 shadow-lg">
    <h4 class="text-lg font-semibold mb-4">Dashboard</h4>
    <nav>
        <a href="{{ url('/home') }}" class="block py-2 px-4 rounded hover:bg-gray-700" style="{{ Request::is('home') ? 'color: blue;' : '' }}">🏠 Home</a>
        <a href="{{ url('/contacts') }}" class="block py-2 px-4 rounded hover:bg-gray-700" style="{{ Request::is('contacts') ? 'color: blue;' : '' }}">📒 My Contacts</a>
        <a href="{{ url('/addcontact') }}" class="block py-2 px-4 rounded hover:bg-gray-700" style="{{ Request::is('addcontact') ? 'color: blue;' : '' }}">➕ Add Contact</a>
        <a href="{{ url('/favourites') }}" class="block py-2 px-4 rounded hover:bg-gray-700" style="{{ Request::is('favourites') ? 'color: blue;' : '' }}">⭐ Favorites</a>
    </nav>
</aside>

<!-- Header -->
<header class="bg-gray-800 p-4 flex justify-between items-center fixed top-0 w-full pl-64 z-40">
    <h2 class="text-xl font-semibold text-white">@yield('header-title')</h2>
    <div class="relative">
        <button onclick="toggleSelect()" id="userButton" class="flex items-center space-x-2 bg-gray px-4 py-2 rounded hover:bg-gray-600">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="User Avatar" class="w-8 h-8 rounded-full">
            @else
                <div class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white font-bold rounded-full">
                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                </div>
            @endif
            <span class="text-white">{{ Auth::user()->first_name }}</span>
        </button>

        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-gray-700 text-white rounded shadow-lg">
            <button onclick="window.location.href = '/profile'" class="w-full text-left px-4 py-2 hover:bg-gray-600">👤 Profile</button>
            <button onclick="logoutUser()" class="w-full text-left px-4 py-2 hover:bg-gray-600">🚪 Logout</button>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</header>

<!-- Main Content -->
<main class="ml-64 mt-16 p-6">
    <div class="mb-4 flex justify-between items-center">
        <!-- Search Bar (Always Shows) -->
        @hasSection('extra-search')
        <input id="searchInput" type="text" placeholder="Search Contacts..." class="w-64 p-2 rounded bg-gray-800 text-white">
            @yield('extra-search')
        @endif
    </div>

    <!-- Page Content -->
    <div id="pageContent">
        @yield('content')
    </div>
</main>
<script>
    document.getElementById("userButton").addEventListener("click", function(event) {
        event.stopPropagation();
        document.getElementById("userDropdown").classList.toggle("hidden");
    });
    document.addEventListener("click", function (event) {
        const dropdown = document.getElementById("userDropdown");
        if (!dropdown.classList.contains("hidden") && !dropdown.contains(event.target)) {
            dropdown.classList.add("hidden");
        }
    });
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const pageContent = document.getElementById("pageContent");
        let currentPageUrl = window.location.pathname;
        searchInput.addEventListener("keyup", async function () {
            let query = this.value.trim();
            if (query === "") {
                fetch(currentPageUrl)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, "text/html");
                        const newContent = doc.getElementById("pageContent").innerHTML;
                        pageContent.innerHTML = newContent;
                    })
                    .catch(error => console.error("Error loading page content:", error));
            } else {
                try {
                    const response = await fetch(`/search-contacts?search=${query}`);
                    const contacts = await response.json();
                    renderContacts(contacts);
                } catch (error) {
                    console.error("Error fetching contacts:", error);
                }
            }
        });

        function renderContacts(contacts) {
            pageContent.innerHTML = `
                <table class="table-auto w-full border-collapse border border-gray-700">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="p-3 border border-gray-700">Fav</th>
                            <th class="p-3 border border-gray-700">Avatar</th>
                            <th class="p-3 border border-gray-700">Name</th>
                            <th class="p-3 border border-gray-700">Email</th>
                            <th class="p-3 border border-gray-700">Phone</th>
                            <th class="p-3 border border-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${contacts.map(contact => `
                            <tr class="border-b border-gray-700 hover:bg-gray-800 transition">
                                <td class="p-3 text-center">
                                    <button onclick="toggleFavorite(${contact.id})" class="text-xl">
                                        ${contact.is_favorite ? "⭐" : "☆"}
                                    </button>
                                </td>
                                <td class="p-3 text-center">
                                    <img src="${contact.avatar_url || 'https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png'}" class="w-10 h-10 rounded-full mx-auto">
                                </td>
                                <td class="p-3">${contact.first_name} ${contact.last_name}</td>
                                <td class="p-3">${contact.email}</td>
                                <td class="p-3">${contact.phone}</td>
                                <td class="p-3 flex gap-2 justify-center h-[55px]">
                                    <a href="/edit-contact/${contact.id}" class="bg-yellow-500 px-3 py-1 rounded hover:bg-yellow-600 transition">Edit</a>
                                    <form action="/contacts/${contact.id}" method="POST" onclick="return handleDelete(event, ${contact.id})">
                                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 transition">Delete</button>
                                    </form>                                
                                </td>
                            </tr>`).join('')}
                    </tbody>
                </table>`;
        }
        async function toggleFavorite(contactId) {
            try {
                const response = await fetch(`/contacts/${contactId}/toggle-favorite`, { 
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
                    }
                });

                if (response.ok) {
                    location.reload(); // Reload to reflect updated favorite status
                } else {
                    console.error('Failed to toggle favorite');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        async function handleDelete(event, contactId) {
            event.preventDefault();
            if (confirm('Are you sure?')) {
                try {
                    const form = event.target;
                    const response = await fetch(form.action, { method: 'POST', body: new FormData(form) });
                    if (response.ok) {
                        alert('Contact deleted successfully!');
                        searchInput.dispatchEvent(new Event('keyup'));
                    } else {
                        alert('Failed to delete contact.');
                    }
                } catch (error) {
                    console.error('Error deleting contact:', error);
                }
            }
        }
        function logoutUser() {
        if (confirm("Are you sure you want to logout?")) {
            document.getElementById("logout-form").submit();
            window.location.href('/');
        }
    }
    });
    document.addEventListener("DOMContentLoaded", () => {
    tailwind.config = { corePlugins: { preflight: true } };
});

</script>
</body>
</html>

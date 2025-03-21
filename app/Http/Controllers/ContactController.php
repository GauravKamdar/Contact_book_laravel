<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    function create(){
        return view('add-contact');
    }
    public function addContact(Request $request)
    {
    // Validate input
    $request->validate([
        'fname' => 'required|min:3|max:255',
        'lname' => 'required',
        'email' => 'required|email|unique:contacts',
        'phone' => 'required|digits:10',
        'about' => 'nullable|string',
        'address' => 'nullable|string',
        'wpnumber' => 'nullable|digits:10',
        'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Get the logged-in user's ID
    $userId = Auth::id(); 

    //handle file upload

        // Handle avatar upload if provided
        $avatarName = null;
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '.' . $avatarFile->getClientOriginalExtension(); // Generate a unique file name
            $avatarFile->storeAs('avatars', $avatarName, 'public'); // Store in 'avatars' directory
        } 

    // Ensure contacts.id matches users.id
    Contact::create([
        'id' => $userId,  // Manually set contacts.id to match users.id
        'first_name' => $request->fname,
        'last_name' => $request->lname,
        'email' => $request->email,
        'phone' => $request->phone,
        'about' => $request->about,
        'address' => $request->address,
        'whatsapp_number' => $request->wpnumber,
        'user_id' => $userId,
        'avatar_url' => $avatarName,
    ]);

    return redirect('/home')->with('success', 'Contact added successfully!');   
    }

    public function editContact($id)
    {
    // Fetch contact details by ID
    $contact = Contact::findOrFail($id);

    // Return the edit view with the contact data
    return view('edit-contact', compact('contact'));
    }

    public function uploadAvatar(Request $request, $id)
    {
        $request->validate([
            'avatar_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $contact = Contact::findOrFail($request->id);
        if ($request->hasFile('avatar_url')) {
            $avatarFile = $request->file('avatar_url');
            $avatarName = time() . '.' . $avatarFile->getClientOriginalExtension(); // Generate a unique file name
            $avatarFile->storeAs('avatars', $avatarName, 'public'); // Store in 'avatars' directory
            $contact->avatar_url = $avatarName; // Save only the file name
        }  
        $contact->save();
        return redirect()->route('contacts.index')->with('success', 'Avatar updated successfully!');
    }

    public function removeAvatar($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->avatar_url) {
            Storage::delete('public/avatars/' . $contact->avatar_url);
            $contact->avatar_url = null;
            $contact->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function updateContact(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'fname' => 'required|min:3|max:255',
            'lname' => 'required',
            'email' => 'required|email|unique:contacts,email,'.$id, // Ignore the current contact email
            'phone' => 'required|digits:10',
            'about' => 'nullable|string',
            'address' => 'nullable|string',
            'wpnumber' => 'nullable|digits:10',
        ]);

        // Find the existing contact
        $contact = Contact::findOrFail($id);
        
        // Update contact details
        $contact->update([
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'about' => $request->about,
            'address' => $request->address,
            'whatsapp_number' => $request->wpnumber,
        ]);
        $contact->save();
        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully!');
    }

    public function favorites()
    {
        $contacts = Contact::where('user_id', auth()->id())
                           ->where('is_favorite', true)
                           ->paginate(10);

        return view('favorites', compact('contacts'));
    }

    // Toggle favorite status
    public function toggleFavorite($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_favorite = !$contact->is_favorite; // Toggle status
        $contact->save();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        return response()->json(
            Contact::where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->get()
                ->map(function ($contact) {
                    $contact->avatar_url = $contact->avatar_url
                        ? asset('storage/avatars/' . $contact->avatar_url)
                        : 'https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png';
                    return $contact;
                })
        );
    }
    public function searchFavorites(Request $request)
    {
        return response()->json(
            Contact::where('is_favorite', true)
                ->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'like', "%{$request->search}%");
                })
                ->get()
        );
    }
}

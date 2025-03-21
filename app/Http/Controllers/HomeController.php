<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth; 
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userId = Auth::id(); // Get logged-in user ID

        $contacts = Contact::where('user_id', $userId)
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
            })
            ->latest() // Orders by latest created_at
            ->take(5) // Limits to last 5 records
            ->get();  // Use get() instead of paginate() for a fixed number of results
    

        return view('home', ['contacts' => $contacts]);
    }
    public function contact()
    {
        $userId = Auth::id(); // Get the logged-in user ID

        $contacts = Contact::where('user_id', $userId)
            ->latest() // Orders by latest created_at
            ->simplePaginate(8);

        return view('my-contacts', ['contacts' => $contacts]);
    }
    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Contact deleted successfully!');
    }

    public function profile() {
        return view('profile', ['user' => Auth::user()]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = Auth::user();
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '.' . $avatarFile->getClientOriginalExtension(); // Generate a unique file name
            $avatarFile->storeAs('avatars', $avatarName, 'public'); // Store in 'avatars' directory
            $user->avatar = $avatarName; // Save only the file name
        }  
        $user->save();

        return back()->with('success', 'Avatar updated successfully!');
    }

    public function update(Request $request) {
        $user = Auth::user();

        // Validate the form data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Update user details
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Save changes
        $user->save();
        return redirect()->route('contacts.index')->with('success', 'Profile updated successfully!');
    }
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            // Delete the file from storage
            Storage::delete('public/avatars/' . $user->avatar);

            // Remove the avatar from the database
            $user->avatar = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Avatar removed successfully.');
    }
}
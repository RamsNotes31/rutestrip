<?php
namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\HikingRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * User Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'favorites' => $user->favorites()->count(),
            'searches'  => $user->searchHistories()->count(),
            'comments'  => $user->comments()->count(),
            'ratings'   => $user->ratings()->count(),
        ];

        $recentFavorites = $user->favoriteRoutes()->latest('favorites.created_at')->take(4)->get();
        $recentSearches  = $user->searchHistories()->latest()->take(5)->get();

        return view('user.dashboard', compact('user', 'stats', 'recentFavorites', 'recentSearches'));
    }

    /**
     * API endpoint for realtime dashboard stats
     */
    public function statsApi()
    {
        $user = Auth::user();

        return response()->json([
            'favorites' => $user->favorites()->count(),
            'searches'  => $user->searchHistories()->count(),
            'comments'  => $user->comments()->count(),
            'ratings'   => $user->ratings()->count(),
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Show all favorites
     */
    public function favorites()
    {
        $favorites = Auth::user()->favoriteRoutes()->paginate(12);
        return view('user.favorites', compact('favorites'));
    }

    /**
     * Toggle favorite route
     */
    public function toggleFavorite(HikingRoute $route)
    {
        $user     = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('hiking_route_id', $route->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Rute dihapus dari favorit.');
        }

        Favorite::create([
            'user_id'         => $user->id,
            'hiking_route_id' => $route->id,
        ]);

        return back()->with('success', 'Rute ditambahkan ke favorit!');
    }

    /**
     * Search history
     */
    public function history()
    {
        $histories = Auth::user()->searchHistories()->latest()->paginate(20);
        return view('user.history', compact('histories'));
    }

    /**
     * Edit profile form
     */
    public function editProfile()
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}

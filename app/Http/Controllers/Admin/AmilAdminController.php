<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AmilAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->orderBy('is_amil', 'desc')
        ->orderBy('name')
        ->paginate(20);

        return view('admin.amils', compact('users', 'search'));
    }

    public function toggle(User $user)
    {
        $user->update([
            'is_amil' => !$user->is_amil,
        ]);

        $status = $user->is_amil ? 'appointed as' : 'removed from';
        return redirect()->route('admin.amils')
            ->with('success', "{$user->name} has been {$status} Amil.");
    }
}

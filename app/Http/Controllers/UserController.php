<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
        return response()->json(User::all());
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);
    
        $existingUser = User::where('name', $request->name)->where('email', $request->email)->first();
    
        if ($existingUser) {
            return redirect()->route('users.index')->with('error', 'User already exists.');
        }
    
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'Success',
        ]);
    
        return redirect()->route('users.index')->with('success', 'User added successfully.');
    }
}

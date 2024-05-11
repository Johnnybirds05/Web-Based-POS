<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function store(Request $req)
    {
        // return $req;
        $req->validate([
            'image' => ['required', 'mimes:jpeg,jpg,png'],
            'username' => ['required', 'string', 'unique:users,username'],
            'last_name' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'role' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'contact' => ['required', 'string', 'regex:/^(\+63|0)9\d{9}$/'],
        ]);
        $file = $req->file('image');

        if ($file) {
            $filePath = $file->store('public/avatars');
            $filePathArray = explode('/', $filePath);
            $file_location = $filePathArray[2];
        }

        User::create([
            'img' => $file_location,
            'username' => $req->username,
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'middle_initial' => $req->mname ? $req->middle_name : '',
            'password' => $req->password,
            'contact' => $req->contact,
            'role' => $req->role,
        ]);
        return response()->json([
            "status" => 'user successfully saved!'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return User::where('user_id', $id)->first();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        // Validate the request data
        $req->validate([
            'username' => ['required', 'string', 'unique:users,username,' .$req->user_id.',user_id'],
            'last_name' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'role' => ['required', 'string'],
            'contact' => ['required', 'string', 'regex:/^(\+63|0)9\d{9}$/'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png'], // Define image validation rules
        ]);

        $file = $req->file('image');

        if ($file) {
            $filePath = $file->store('public/avatars');
            $filePathArray = explode('/', $filePath);
            $file_location = $filePathArray[2];
        }

        // Update user data in the database
        $user = User::findOrFail($req->user_id);
        $user->update([
            'img' => $file_location ?? $user->img, // Use the new image location or existing image path
            'username' => $req->username,
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'middle_initial' => $req->middle_name ?? '', // Use provided middle name or empty string
            'contact' => $req->contact,
            'role' => $req->role,
        ]);



        // Return a success response
        return response()->json([
            "status" => 'User successfully updated!',
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        return User::all();
    }
}

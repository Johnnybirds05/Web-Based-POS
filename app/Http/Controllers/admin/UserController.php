<?php

namespace App\Http\Controllers\admin;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $req)
    {
        // return $req;
        $req->validate([
            'image' => ['required', 'mimes:jpeg,jpg,png'],
            'username' =>['required','string','unique:users,username'],
            'last_name' => ['required','string'],
            'first_name' => ['required','string'],
            'role' => ['required','string'],
            'password' => [ 'required','string','min:8'],
            'contact' => ['required', 'string', 'regex:/^(\+63|0)9\d{9}$/'],
        ]);
        $file = $req->file('image');

        if($file){
            $filePath = $file->store('public/avatars');
            $filePathArray = explode('/', $filePath);
            $file_location = $filePathArray[2];
        }

        User::create([
            'img' => $file_location,
            'username' => $req->username,
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'middle_initial' => $req->mname?$req->middle_name:'',
            'password' => $req->password,
            'contact' => $req->contact,
            'role' => $req->role,
        ]);
        return response()->json([
            "status" => 'user successfully saved!'
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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

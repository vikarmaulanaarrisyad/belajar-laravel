<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Mendapatkan users dari database
        $users = User::paginate(1);

        $filterKeyword = $request->get('keyword');

        $status = $request->get('status');

        if ($status) {
            $users = User::where('status', $status)->paginate(1);
        } else {
            $users = User::paginate(1);
        }

        //  check jika ada $filterKeyword maka kita query User
        if ($filterKeyword) {
            // Cek Jika ada status
            if ($status) {
                $users = User::where('email', 'LIKE', "%$filterKeyword%")
                    ->where('status', $status)
                    ->paginate(1);
            }

            $users = User::where('email', 'LIKE', "%$filterKeyword%")->paginate(1);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Menampilkan Sebuah FOrm Tambah User
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Menangkap request dan menyimpan ke database
        $new_user = new User;

        // set properti dari user dengan nilai yang berasal dari data yang dikirim oleh form create user
        $new_user->name = $request->name;
        $new_user->username = $request->username;
        $new_user->username = $request->username;
        $new_user->roles = json_encode($request->roles);
        $new_user->address = $request->address;
        $new_user->email = $request->email;
        $new_user->phone = $request->phone;
        $new_user->password = Hash::make($request->password);

        // Menghandle file upload, apakah ada inputan file gambar
        if ($request->file('avatar')) {
            // Jika File Avatar ada simpan gambar pada folder public avatar
            $file = $request->file('avatar')->store('avatars', 'public');

            // save gambar
            $new_user->avatar = $file;
        }

        // model User baru tadi ke database dengan method save()
        $new_user->save();

        // berhasil menyimpan kita ingin arahkan user kembali ke form create
        return redirect()->route('users.create')->with('status', 'User successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Mencari user dengan id tertentu
        $user = User::findOrfail($id);

        return view('users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Mengambil data user yang akan diedit lalu lempar ke view
        $user = User::findOrfail($id);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Menangkap request edit dan mengupdate ke database
        $user = User::findOrfail($id);

        //  nilai yang berasal dari form
        $user->name = $request->get('name');
        $user->roles = json_encode($request->get('roles'));
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->status = $request->get('status');

        // Cek file gambar
        if ($request->file('avatar')) {
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                // Untuk menghapus file kita gunakan
                Storage::delete('public/' . $user->avatar);
            }
            $file = $request->file('avatar')->store('avatars', 'public');

            $user->avatar = $file;

            return redirect()->route('users.edit', [$id])->with('status', 'User succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrfail($id);

        // Fungsi Delete
        $user->delete();

        return redirect()->route('users.index')->with('status', 'User successfully deleted');
    }
}

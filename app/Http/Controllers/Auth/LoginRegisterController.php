<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginRegisterController extends Controller
{
    public function index()
    {
        //get Data db
        $users - User::latest()->paginate(10);

        return view('admin.akun.index', compact('users'));
    }
    public function create()
    {
    return view('admin.akun.create');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
            
        ]);
        
        // Buat user baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype' => 'admin'
        ]);

        // Autentikasi user
        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();

        // Cek tipe user dan arahkan ke dashboard yang sesuai
        if ($request->user()->usertype == 'admin') {
            return redirect('admin/dashboard')->with('success', 'You have successfully registered & logged in!');
        }
        return redirect()->intended(route('dashboard'));
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validasi data input login
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek kredensial
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek tipe user dan arahkan ke dashboard yang sesuai
            if ($request->user()->usertype == 'admin') {
                return redirect('admin/dashboard')->with('success', 'You have successfully logged in!');
            }
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate session token
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have logged out successfully!');
    }



public function update(Request $request, $id): RedirectResponse
{
    //validate form
    $validated = $request->validate([
        'name' => 'required|string|max:250',
        'usertype' => 'required'
    ]);

    //get post by ID
    $datas = User::findOrFail($id);
    //edit akun

    $datas->update([
        'name' => $request->name,
        'usertype' => $request->usertype

    ]);

    //redirect to index
    return redirect()->route('akun.edit', $id)->with(['success' => 'Data Berhasil Diubah!']);
}

public function updateEmail(Request $request, $id): RedirectResponse
{
    //validate form
    $validated = $request->validate([
        'email' => 'required|email|max:250|unique:users'
    ]);

    //get post by ID
    $datas = User::findOrFail($id);
    //edit akun

    $datas->update([
        'email' => $request->email

    ]);

    //redirect to index
    return redirect()->route('akun.edit', $id)->with(['success' => 'Email Berhasil Diubah!']);
}

public function updatePassword(Request $request, $id): RedirectResponse
{
    //validate form
    $validated = $request->validated([
        'password' => 'required|min:8|confirmed'
    ]);

    //get post byID
    $datas = User::findOFail($id);
    //edit akun

    $datas->update([
        'password' =>Hash::make($request->password)
    ]);

    //redireect to index
    return redirect()->route('akun.edit', $id)->with(['success' => 'PASSWORD Berhasil Diubah!']);
}




// Hapus Data 
public function destroy($id): RedirectResponse
{

    //cari id siswa
    $siswa = DB::table('siswas')->where('id_user', $id)->value('id');

    //jika siswa
    if ($siswa) {
        //delete siswa
        $this->destroySiswa($siswa);
    }

    //get post by ID
    $post = User::findOrFail($id);

    //delete post
    $post->delete();

    //redirect to index
    return redirect()->route('akun.index')->with(['success' => 'Akun Berhasil Diubah!']);
}

public function destroySiswa(string $id)
{
    //get id siswa
    $post = Siswa::findOrFail($id);

    //delete image
    Strage::delete('public/siswas/' . $post->image);

    //delete post
    $post->delete();
}}
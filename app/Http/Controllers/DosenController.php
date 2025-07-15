<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ['search' => $request->get('search', ''), 'show' => $request->get('show', 10)];
        $data = Dosen::orderBy('nip')->where('nama', 'like', '%'.$query['search'].'%')->orWhere('nip', 'like', '%'.$query['search'].'%')->paginate(function ($total) use ($request, $query) {
            $perPage = $query['show'];
            if($perPage == 'all')
                return $total;
            return $perPage;
        })->appends($query)->toResourceCollection();
        return Inertia::render('dosen/DosenIndex', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('dosen/TambahDosen');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nip' => ['required', 'unique:dosen', 'numeric'],
            'nama' => ['required'],
            'email' => ['required', 'email'],
            'nomor_telpon' => ['required'],
            'jenis_kelamin' => ['required'],
        ]);
        $user = User::create([
            'email' => $data['email'],
            'email_verified_at' => now(),
            'password' => Hash::make('doseninfor'),
        ]);
        unset($data['email']);
        $data['user_id'] = $user->id;
        // dd($data);
        Dosen::create($data);
        return redirect('/lecturers')->with('alert', ['title' => 'Data dosen berhasil ditambahkan.', 'type' => 'success']);
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
        $dosen = Dosen::with('user')->findOrFail($id);
        return Inertia::render('dosen/EditDosen', ['dosen' => $dosen]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dosen = Dosen::findOrFail($id);
        $prevNIP = $request->input('prevNIP');
        $data = $request->validate([
            'nip' => ['required', 'unique:dosen,nip,'.$prevNIP.',nip', 'numeric'],
            'nama' => ['required'],
            'nomor_telpon' => ['required'],
            'jenis_kelamin' => ['required'],
        ]);
        $dosen->update($data);
        return redirect('/lecturers')->with('alert', ['title' => 'Data dosen berhasil diubah.', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Dosen::destroy($id);
        return redirect('/lecturers')->with('alert', ['title' => 'Data dosen berhasil dihapus.', 'type' => 'success']);
    }

    public function print()
    {
        $list = Dosen::all();
        Pdf::view('print.dosen', ['list' => $list])
        ->disk('public')
        ->format('a4')
        ->headerView('print.header')
        ->margins(30, 25, 30, 25)
        ->save('/laporan/Laporan Dosen.pdf');
        return Storage::disk('public')->download('/laporan/Laporan Dosen.pdf');
    }
}

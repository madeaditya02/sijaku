<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MahasiswaResource;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ['search' => $request->get('search', ''), 'show' => $request->get('show', 10)];
        $data = Mahasiswa::orderBy('nim')->where('nama', 'like', '%'.$query['search'] ?? ''.'%')->orWhere('nim', 'like', '%'.$query['search'].'%')->paginate(function ($total) use ($request, $query) {
            $perPage = $query['show'];
            if($perPage == 'all')
                return $total;
            return $perPage;
        })->appends($query)->toResourceCollection();
        return Inertia::render('mahasiswa/MahasiswaIndex', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('mahasiswa/TambahMahasiswa');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nim' => ['required', 'unique:mahasiswa', 'numeric'],
            'nama' => ['required'],
            'angkatan' => ['required', 'numeric'],
            'nomor_telpon' => ['required'],
            'tempat_lahir' => ['required'],
            'tanggal_lahir' => ['required'],
            'jenis_kelamin' => ['required'],
            'agama' => ['required'],
        ]);
        // dd($data);
        Mahasiswa::create($data);
        return redirect('/students')->with('alert', ['title' => 'Data Mahasiswa berhasil ditambahkan.', 'type' => 'success']);
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
        $mahasiswa = Mahasiswa::findOrFail($id);
        return Inertia::render('mahasiswa/EditMahasiswa', ['mahasiswa' => $mahasiswa]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $prevNIM = $request->input('prevNIM');
        $data = $request->validate([
            'nim' => ['required', 'unique:mahasiswa,nim,'.$prevNIM.',nim', 'numeric'],
            'nama' => ['required'],
            'angkatan' => ['required', 'numeric'],
            'nomor_telpon' => ['required'],
            'tempat_lahir' => ['required'],
            'tanggal_lahir' => ['required'],
            'jenis_kelamin' => ['required'],
            'agama' => ['required'],
        ]);
        $mahasiswa->update($data);
        return redirect('/students')->with('alert', ['title' => 'Data Mahasiswa berhasil diubah.', 'type' => 'success']);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Mahasiswa::destroy($id);
        return redirect('/students')->with('alert', ['title' => 'Data Mahasiswa berhasil dihapus.', 'type' => 'success']);
    }

    public function print()
    {
        $list = Mahasiswa::all();
        Pdf::view('print.mahasiswa', ['list' => $list])
        ->disk('public')
        ->format('a4')
        ->headerView('print.header')
        ->margins(30, 25, 30, 25)
        ->save('/laporan/Laporan Mahasiswa.pdf');
        return Storage::disk('public')->download('/laporan/Laporan Mahasiswa.pdf');
    }
}

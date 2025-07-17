<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use App\Http\Resources\MataKuliahResource;

class MataKuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ['search' => $request->get('search', ''), 'show' => $request->get('show', 10)];
        $data = MataKuliah::orderBy('kode')->where('nama_matakuliah', 'like', '%'.$query['search'].'%')->orWhere('kode', 'like', '%'.$query['search'].'%')->paginate(function ($total) use ($request, $query) {
            $perPage = $query['show'];
            if($perPage == 'all')
                return $total;
            return $perPage;
        })->appends($query)->toResourceCollection();
        return Inertia::render('mata_kuliah/MataKuliahIndex', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('mata_kuliah/TambahMataKuliah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'unique:mata_kuliah'],
            'nama_matakuliah' => ['required'],
            'semester' => ['required', 'numeric'],
            'sks_tatap_muka' => ['required', 'numeric'],
            'sks_praktikum' => ['required', 'numeric'],
            'jenis_matakuliah' => ['required'],
        ]);
        $data['sks'] = $data['sks_tatap_muka'] + $data['sks_praktikum'];
        // dd($data);
        MataKuliah::create($data);
        return redirect('/mata-kuliah')->with('alert', ['title' => 'Data mata kuliah berhasil ditambahkan.', 'type' => 'success']);
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
        $mata_kuliah = new MataKuliahResource(MataKuliah::findOrFail($id));
        return INertia::render('mata_kuliah/EditMataKuliah', ['mata_kuliah' => $mata_kuliah]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mata_kuliah = MataKuliah::findOrFail($id);
        $prevKode = $request->input('prevKode');
        $data = $request->validate([
            'kode' => ['required', 'unique:mata_kuliah,kode,'.$prevKode.',kode'],
            'nama_matakuliah' => ['required'],
            'semester' => ['required', 'numeric'],
            'sks_tatap_muka' => ['required', 'numeric'],
            'sks_praktikum' => ['required', 'numeric'],
            'jenis_matakuliah' => ['required'],
        ]);
        $data['sks'] = $data['sks_tatap_muka'] + $data['sks_praktikum'];
        $mata_kuliah->update($data);
        return redirect('/mata-kuliah')->with('alert', ['title' => 'Data mata kuliah berhasil diubah.', 'type' => 'success']);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        MataKuliah::destroy($id);
        return redirect('/mata-kuliah')->with('alert', ['title' => 'Data mata kuliah berhasil dihapus.', 'type' => 'success']);
    }
}

<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;
use App\Models\MataKuliahTawar;
use App\Http\Resources\MataKuliahTawarResource;

class KRSController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->mahasiswa) {
            return abort(403);
        }
        $semesterIni = semesterIni();
        $smt;
        if ($request->semester && $request->tahun_1 && $request->tahun_2) {
            $smt = [
            'semester' => $request->semester,
            'tahun_ajaran_pertama' => $request->tahun_1,
            'tahun_ajaran_kedua' => $request->tahun_2,
            ];
        } else {
            $smt = semesterIni();
        }
        $semester = MataKuliahTawar::select(['semester', 'tahun_ajaran_pertama', 'tahun_ajaran_kedua'])->whereHas('krs', function ($query) {
            $query->where('mahasiswa.nim', auth()->user()->mahasiswa->nim);
        })->distinct()->get()->map(fn($val) => $val->toArray());
        if ($semester->last() != $semesterIni) {
            $semester->push($semesterIni);
        }

        $query = $request->only(['semester', 'tahun_1', 'tahun_2', 'show']);
        $matkulKRS = MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->whereHas('krs', function ($query) {
            $query->where('mahasiswa.nim', auth()->user()->mahasiswa->nim);
        })->paginate(function ($total) use ($request) {
            $perPage = $request->get('show', 10);
            if($perPage == 'all')
                return $total;
            return $perPage;
        })->appends($query)->toResourceCollection();

        $adaPerkuliahan = Perkuliahan::whereHas('jadwal', function ($query) use ($smt) {
            $query->whereHas('mataKuliahTawar', function ($q) use ($smt) {
                $q->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->where('semester', $smt['semester']);
            });
        })->count();

        return Inertia::render('krs/KRSIndex', [
            'semester' => $semester,
            'semesterIni' => $smt,
            'matkulKRS' => $matkulKRS,
            'adaPerkuliahan' => $adaPerkuliahan
        ]);
        // $semester = auth()->user()->mahasiswa->krs()->select(['semester', 'tahun_ajaran_pertama', 'tahun_ajaran_kedua'])->get();
    }

    public function ajukan(Request $request)
    {
        if (!auth()->user()->mahasiswa) {
            return abort(403);
        }
        // $smt;
        // if ($request->semester && $request->tahun_1 && $request->tahun_2) {
        //     $smt = [
        //     'semester' => $request->semester,
        //     'tahun_ajaran_pertama' => $request->tahun_1,
        //     'tahun_ajaran_kedua' => $request->tahun_2,
        //     ];
        // } else {
        // }
        $smt = semesterIni();
        $query = ['search' => $request->get('search', ''), 'show' => $request->get('show', 10)];
        // dd($smt);
        $matkulKRS = MataKuliahTawar::where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->whereHas('krs', function ($query) {
            $query->where('mahasiswa.nim', auth()->user()->mahasiswa->nim);
        })->select(['id'])->get()->pluck('id');
        $listMatkul = MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->withCount('krs')->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])
        ->whereHas('mata_kuliah', function ($q) use ($query) {
            $q->where('nama_matakuliah', 'like', '%'.$query['search'].'%')->orWhere('kode', 'like', '%'.$query['search'].'%');
        })
        ->paginate(function ($total) use ($request) {
            $perPage = $request->get('show', 10);
            if($perPage == 'all')
                return $total;
            return $perPage;
        })->appends($query)->toResourceCollection();
        return Inertia::render('krs/AjukanKRS', [
            'matkulKRS' => $matkulKRS,
            'listMatkul' => $listMatkul,
        ]);
    }
    public function detail($id, Request $request)
    {
        $matkul = new MataKuliahTawarResource(MataKuliahTawar::with(['dosen', 'krs', 'mata_kuliah'])->findOrFail($id));
        return Inertia::render('krs/DetailKRS', ['matkul' => $matkul]);
    }
    public function pilihMatkul($id, Request $request)
    {
        if ($request->select == true) {
            auth()->user()->mahasiswa->krs()->attach($id);
        } else {
            auth()->user()->mahasiswa->krs()->detach($id);
        }
    }
}

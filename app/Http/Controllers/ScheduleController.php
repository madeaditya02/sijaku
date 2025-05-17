<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use App\Models\MataKuliahTawar;
use App\Http\Controllers\Controller;
use App\Http\Resources\DosenResource;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\PerkuliahanResource;
use App\Http\Resources\MataKuliahTawarResource;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->admin) {
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
            $semuaSmt = semuaSemester();
            $matkulSemesterIni = MataKuliahTawarResource::collection(MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->get());
            // $matkulSemesterIni = MataKuliahTawarResource::collection(MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->get());
            return Inertia::render('schedules/ScheduleAdmin', [
                'semester' => $semuaSmt,
                'semesterIni' => $smt,
                'matkulSemester' => $matkulSemesterIni,
                'listRuangan' => Ruangan::all(['id_ruangan', 'nama_ruangan', 'kapasitas'])
            ]);
        }
    }
    public function tambahMataKuliah(Request $request)
    {
        // Ambil semester dari url, default semester sekarang
        $params = $request->all();
        if (isset($params['semester']) && isset($params['tahun_1']) && isset($params['tahun_2'])) {
            $params['semester'] = $params['semester'];
            $params['tahun_ajaran_pertama'] = $params['tahun_1'];
            $params['tahun_ajaran_kedua'] = $params['tahun_2'];
        } else {
            $params = semesterIni();
        }
        return Inertia::render('schedules/TambahMatkul', [
            'semester' => collect($params)->only(['semester', 'tahun_ajaran_pertama', 'tahun_ajaran_kedua'])->all(),
            'mata_kuliah' => MataKuliah::all(),
            'listDosen' => DosenResource::collection(Dosen::all()),
        ]);
    }
    public function submitMataKuliah(Request $request)
    {
        $form = $request->validate([
            'mata_kuliah' => ['required'],
            'dosen' => ['required', 'exists:dosen,nip'],
            'semester' => ['required'],
            'tahun_ajaran_pertama' => ['required', 'integer'],
            'tahun_ajaran_kedua' => ['required', 'integer'],
            'kelas' => ['required', 'integer'],
        ]);
        $data = [];
        foreach ($form['mata_kuliah'] as $matkul) {
            for ($i=65; $i < 65+$form['kelas']; $i++) { 
                array_push($data, [
                    'id_matkul' => $matkul['kode'],
                    'tahun_ajaran_pertama' => $form['tahun_ajaran_pertama'],
                    'tahun_ajaran_kedua' => $form['tahun_ajaran_kedua'],
                    'kelas' => chr($i),
                    'semester' => $form['semester'],
                    'dosen_ketua' => $form['dosen'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        MataKuliahTawar::insert($data);
        return to_route('schedules')->with('alert', ['title' => 'Mata kuliah berhasil ditambahkan.', 'text' => 'Silahkan tentukan jadwal untuk mata kuliah tersebut.', 'type' => 'success']);
    }

    public function ubahJadwal($matkul, Request $request)
    {
        $form = $request->validate([
            'hari' => ['required'],
            'jam_mulai' => ['required'],
            'jam_selesai' => ['required'],
            'id_ruangan' => ['required'],
        ]);
        $form['id_matkul_tawar'] = $matkul;
        Jadwal::upsert($form, ['id_matkul_tawar']);
        return to_route('schedules')->with('alert', ['title' => 'Jadwal mata kuliah.', 'text' => 'Jadwal mata kuliah berhasil diubah.', 'type' => 'success']);
    }

    public function detailMataKuliah(MataKuliahTawar $matkul)
    {
        $matkul->load(['jadwal', 'jadwal.ruangan', 'dosen']);
        return Inertia::render('schedules/DetailMatkul', [
            'mata_kuliah' => new MataKuliahTawarResource($matkul),
            'perkuliahan' => PerkuliahanResource::collection($matkul->jadwal->perkuliahan)
        ]);
    }
}

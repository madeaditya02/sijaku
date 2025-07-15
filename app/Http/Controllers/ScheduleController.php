<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;
use App\Models\MataKuliahTawar;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Http\Controllers\Controller;
use App\Http\Resources\DosenResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
            $query = $request->only(['semester', 'tahun_1', 'tahun_2', 'show']);
            // $matkulSemesterIni = MataKuliahTawarResource::collection(MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->paginate($request->show ?? 6)->appends($query));
            $matkulSemesterIni = MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->paginate(function ($total) use ($request) {
                $perPage = $request->get('show', 6);
                if($perPage == 'all')
                    return $total;
                return $perPage;
            })->appends($query)->toResourceCollection();
            // $matkulSemesterIni = $request->show == 'all' ? MataKuliahTawarResource::collection($matkulSemesterIni->get()) : $matkulSemesterIni->paginate($request->show ?? 6)->appends($query)->toResourceCollection();
            // $matkulSemesterIni = MataKuliahTawarResource::collection(MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->get());
            return Inertia::render('schedules/ScheduleAdmin', [
                'semester' => $semuaSmt,
                'semesterIni' => $smt,
                'matkulSemester' => $matkulSemesterIni,
                'listRuangan' => Ruangan::all(['id_ruangan', 'nama_ruangan', 'kapasitas'])
            ]);
        } elseif (auth()->user()->dosen) {
            $dosen = auth()->user()->dosen;
            $start = $request->start ? Carbon::parse($request->start) : now()->startOfWeek();
            $end = $request->end ? Carbon::parse($request->end) : now()->endOfWeek();
            $perkuliahan = PerkuliahanResource::collection(Perkuliahan::whereHas('jadwal.mataKuliahTawar.dosen', function ($query) use ($dosen) {
                $query->where('nip', $dosen->nip);
            })->where(fn ($query) => $query->whereDate('waktu_mulai', '>=', $start)->whereDate('waktu_mulai', '<=', $end))
            ->orWhere(fn ($query) => $query->whereDate('rescheduled_time_start', '>=', $start)->whereDate('rescheduled_time_end', '<=', $end))->orderBy('waktu_mulai')->orderBy('rescheduled_time_start')->get());
            return Inertia::render('schedules/dosen/ScheduleDosen', [
                'perkuliahan' => $perkuliahan,
                'start' => ['year' => $start->year, 'month' => $start->month, 'day' => $start->day],
                'end' => ['year' => $end->year, 'month' => $end->month, 'day' => $end->day],
            ]);
        } else {
            $mahasiswa = auth()->user()->mahasiswa;
            $start = $request->start ? Carbon::parse($request->start) : now()->startOfWeek();
            $end = $request->end ? Carbon::parse($request->end) : now()->endOfWeek();
            $perkuliahan = PerkuliahanResource::collection(Perkuliahan::whereHas('jadwal.mataKuliahTawar.krs', function ($query) use ($mahasiswa) {
                $query->where('mahasiswa.nim', $mahasiswa->nim);
            })->where(fn ($query) => $query->whereDate('waktu_mulai', '>=', $start)->whereDate('waktu_mulai', '<=', $end))
            ->orWhere(fn ($query) => $query->whereDate('rescheduled_time_start', '>=', $start)->whereDate('rescheduled_time_end', '<=', $end))->orderBy('waktu_mulai')->orderBy('rescheduled_time_start')->get());
            return Inertia::render('schedules/ScheduleMahasiswa', [
                'perkuliahan' => $perkuliahan,
                'start' => ['year' => $start->year, 'month' => $start->month, 'day' => $start->day],
                'end' => ['year' => $end->year, 'month' => $end->month, 'day' => $end->day],
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
            'kuota' => ['required', 'integer'],
        ]);
        $data = [];
        foreach ($form['mata_kuliah'] as $matkul) {
            for ($i=65; $i < 65+$form['kelas']; $i++) { 
                array_push($data, [
                    'id_matkul' => $matkul['kode'],
                    'tahun_ajaran_pertama' => $form['tahun_ajaran_pertama'],
                    'tahun_ajaran_kedua' => $form['tahun_ajaran_kedua'],
                    'kelas' => chr($i),
                    'kuota' => $form['kuota'],
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
        $matkul = MataKuliahTawar::findOrFail($matkul);
        $adaJadwal = Jadwal::where('hari', $form['hari'])
        ->where(function ($q) use ($form) {
            $q->where(function ($query) use ($form) {
                $query->where('jam_mulai', '<', $form['jam_selesai'])->where('jam_selesai', '>', $form['jam_selesai']);
            })->orWhere(function ($query) use ($form) {
                $query->where('jam_mulai', '<', $form['jam_mulai'])->where('jam_selesai', '>', $form['jam_mulai']);
            });
        })->whereHas('mataKuliahTawar', function ($query) use ($matkul, $request) {
            $query->where('mata_kuliah_tawar.dosen_ketua', $matkul->dosen_ketua)->where('semester', $request->semester)->where('tahun_ajaran_pertama', $request->tahun_ajaran_pertama)->where('tahun_ajaran_kedua', $request->tahun_ajaran_kedua);
        })->count();
        // dd($adaJadwal);
        if ($adaJadwal > 0) {
            // throw new Exception("Ada jadwal lain", 1);
            return back()->withErrors(['jadwal' => 'Sudah ada jadwal lain di waktu tersebut']);
        }
        $form['id_matkul_tawar'] = $matkul->id;
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
    
    public function download(Request $request)
    {
        $smt = $request->all();
        if (isset($smt['semester']) && isset($smt['tahun_1']) && isset($smt['tahun_2'])) {
            $smt['semester'] = $smt['semester'];
            $smt['tahun_ajaran_pertama'] = $smt['tahun_1'];
            $smt['tahun_ajaran_kedua'] = $smt['tahun_2'];
        } else {
            $smt = semesterIni();
        }
        $list;
        if ($request->type == 'mata_kuliah_tawar') {
            $list = MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->get();
        } else {
            $list = DB::table('mata_kuliah_tawar')->join('mata_kuliah', 'mata_kuliah_tawar.id_matkul', '=', 'mata_kuliah.kode')->select('id_matkul', 'mata_kuliah.nama_matakuliah', 'mata_kuliah.semester', DB::raw('count(id_matkul) as jumlah'))->groupBy('id_matkul')->where('mata_kuliah_tawar.semester', 'Genap')->where('tahun_ajaran_pertama', 2024)->where('tahun_ajaran_kedua', 2025)->get();
        }
        $jumlahMhs = Mahasiswa::count();
        $mhsKRS = Mahasiswa::whereHas('krs', function ($query) use ($smt) {
            $query->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua']);
        })->count();
        $matkul = MataKuliah::count();
        $matkulTawar = MataKuliahTawar::where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->count();
        $fileName = $request->type == 'mata_kuliah_tawar' ?
        'laporan/Laporan Mata Kuliah Tawar-'.$smt['semester'].'-'.$smt['tahun_ajaran_pertama'].'-'.$smt['tahun_ajaran_kedua'].'.pdf' :
        'laporan/Laporan Mata Kuliah-'.$smt['semester'].'-'.$smt['tahun_ajaran_pertama'].'-'.$smt['tahun_ajaran_kedua'].'.pdf';
        Pdf::view($request->type == 'mata_kuliah_tawar' ? 'print.mata-kuliah-tawar' : 'print.mata-kuliah', [
            'list' => $list,
            'jumlahMhs' => $jumlahMhs,
            'mhsKRS' => $mhsKRS,
            'matkul' => $matkul,
            'matkulTawar' => $matkulTawar,
            'smt' => $smt,
        ])
        ->disk('public')
        ->format('a4')
        ->headerView('print.header')
        ->margins(30, 25, 30, 25)
        ->save($fileName);
        return Storage::disk('public')->download($fileName);
        // return view('print.mata-kuliah', ['list' => $list]);
        // return Inertia::render('Welcome');
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Dosen;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;
use App\Models\MataKuliahTawar;
use App\Http\Resources\PerkuliahanResource;
use App\Http\Resources\MataKuliahTawarResource;
use Illuminate\Contracts\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        // Mahasiswa
        $user = User::find(1);
        $user->load(['mahasiswa', 'dosen']);
        $mhs = $user->mahasiswa;
        // Mengambil jadwal kuliah minggu ini/minggu tertentu dari mahasiswa
        $now = Carbon::parse("2023-09-04");
        // $mhs->load(['krs', 'krs.jadwal', 'krs.jadwal.perkuliahan' => function (Builder $query) use ($now) {
        //     $query->whereDate('waktu_mulai', '>=', $now->copy()->startOfWeek())
        //     ->whereDate('waktu_selesai', '<=', $now->copy()->endOfWeek());
        // }]);
        // $kuliahMingguIni = $mhs->krs->pluck('jadwal')->pluck('perkuliahan')->flatten()->filter()->values();
        // $kuliahMingguIni = $mhs->krs->pluck('jadwal')->pluck('perkuliahan')->flatten()->filter()->values();
        // Mengambil kuliah hari ini
        // $kuliahHariIni = $kuliahMingguIni->filter(function($kuliah) {
        //     return $kuliah->waktu_mulai->isToday();
        // });
        $kuliahMingguIni = Perkuliahan::whereHas('jadwal.mataKuliahTawar.krs', function (Builder $query) use ($mhs) {
            $query->where('krs.nim', $mhs->nim);
        })
        ->where(function (Builder $query) use ($now) {
            $query->whereDate('waktu_mulai', '>=', $now->copy()->startOfWeek())->whereDate('waktu_mulai', '<=', $now->copy()->endOfWeek());
        })
        ->orWhere(function (Builder $query) use ($now) {
            $query->whereDate('rescheduled_time', '>=', $now->copy()->startOfWeek())->whereDate('rescheduled_time', '<=', $now->copy()->endOfWeek());
        })
        ->with(['jadwal.mataKuliahTawar.dosen', 'jadwal.ruangan'])->get();
        $kuliahHariIni = $kuliahMingguIni->filter(function($kuliah) {
            return $kuliah->waktu_mulai->isToday();
        });
        // dd($kuliahMingguIni);
        // List KRS Mahasiswa (Beserta List Mahasiswa)
        $krs = $mhs->krs()->with(['mata_kuliah','krs'])->get();
        // dd($krs);

        // Dosen
        $dosen = Dosen::find("111");
        $kuliahMingguIni = Perkuliahan::whereHas('jadwal.mataKuliahTawar.dosen', function (Builder $query) use ($dosen) {
            $query->where('nip', $dosen->nip);
        })
        ->where(function (Builder $query) use ($now) {
            $query->whereDate('waktu_mulai', '>=', $now->copy()->startOfWeek())->whereDate('waktu_mulai', '<=', $now->copy()->endOfWeek());
        })
        ->orWhere(function (Builder $query) use ($now) {
            $query->whereDate('rescheduled_time', '>=', $now->copy()->startOfWeek())->whereDate('rescheduled_time', '<=', $now->copy()->endOfWeek());
        })
        ->with(['jadwal.mataKuliahTawar', 'jadwal.ruangan'])->orderBy('waktu_mulai')->get();
        $dataKuliah = [];
        $belumDiAcc = [];
        $dates = [];
        $day = $now->copy()->startOfWeek();
        for ($i=0; $i < 7; $i++) {
            $dates[$i] = $day->day;
            $belumDiAcc[$i] = $kuliahMingguIni->filter(fn($k) => $k->waktu_mulai->isSameDay($day) && $k->status == 'Pending')->count();
            $dataKuliah[$i] = $kuliahMingguIni->filter(fn($k) => $k->waktu_mulai->isSameDay($day) && ($k->status == 'Hadir' || $k->status == 'Rescheduled'));
            $day = $day->addDay();
        }
        $range = [Carbon::parse("2023-09-05"), Carbon::parse("2023-09-08")];
        $kuliahRange = Perkuliahan::whereHas('jadwal.mataKuliahTawar.dosen', function (Builder $query) use ($dosen) {
            $query->where('nip', $dosen->nip);
        })
        ->where(function (Builder $query) use ($range) {
            $query->whereDate('waktu_mulai', '>=', $range[0])->whereDate('waktu_mulai', '<=', $range[1]);
        })
        ->orWhere(function (Builder $query) use ($range) {
            $query->whereDate('rescheduled_time', '>=', $range[0])->whereDate('rescheduled_time', '<=', $range[1]);
        })
        ->with(['jadwal.mataKuliahTawar', 'jadwal.ruangan'])->orderBy('waktu_mulai')->get();
        // dd($kuliahRange);

        // Admin
        $semuaPerkuliahan = Perkuliahan::where(function (Builder $query) use ($now) {
            $query->whereDate('waktu_mulai', '>=', $now->copy()->startOfDay())->whereDate('waktu_mulai', '<=', $now->copy()->endOfDay());
        })
        ->orWhere(function (Builder $query) use ($now) {
            $query->whereDate('rescheduled_time', '>=', $now->copy()->startOfDay())->whereDate('rescheduled_time', '<=', $now->copy()->endOfDay());
        })->with(['jadwal.mataKuliahTawar.dosen', 'jadwal.mataKuliahTawar.mata_kuliah', 'jadwal.ruangan'])->get();
        $belumDiAcc = $semuaPerkuliahan->filter(fn ($k) => $k->status == 'Pending');
        $statistik = [
            'total' => $semuaPerkuliahan->count(),
            'acc' => $semuaPerkuliahan->filter(fn ($k) => ($k->status == 'Hadir' || $k->status == 'Rescheduled'))->count(),
            'pending' => $belumDiAcc->count(),
        ];
        $semesterYangAda = MataKuliahTawar::select(['semester', 'tahun_ajaran_pertama', 'tahun_ajaran_kedua'])->distinct()->get();
        // dd($semesterYangAda->map(fn($smt) => $smt->toArray())->all());
        $smt = $semesterYangAda[0];
        $matkulSemester = MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->get();
        // return Inertia::render('Test', ['perkuliahan' => MataKuliahTawarResource::collection($matkulSemester)]);
        // dd(PerkuliahanResource::collection($semuaPerkuliahan));
    }
}

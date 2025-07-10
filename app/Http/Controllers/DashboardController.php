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
    public function index(Request $request)
    {
        if ($request->user()->admin) {
            $perkuliahanHariIni = Perkuliahan::with(['jadwal.mataKuliahTawar', 'jadwal.ruangan'])->whereDate('waktu_mulai', '>=', today())->whereDate('waktu_mulai', '<=', today()->endOfDay())->get();
            return Inertia::render('dashboard/DashboardAdmin', [
                'total_kelas' => $perkuliahanHariIni->count(),
                'total_terkonfirmasi' => $perkuliahanHariIni->filter(fn ($kuliah) => $kuliah->status == 'Hadir' || $kuliah->status == 'Rescheduled')->count(),
                'total_belum_konfirm' => $perkuliahanHariIni->filter(fn ($kuliah) => $kuliah->status == 'Pending')->count(),
                'perkuliahan_belum_konfirm' => PerkuliahanResource::collection($perkuliahanHariIni->filter(fn ($kuliah) => $kuliah->status == 'Pending'))
            ]);
        } elseif ($request->user()->dosen) {
            $waktu = $request->date ? Carbon::parse($request->date) : today();
            $dosen = auth()->user()->dosen;
            $perkuliahan = PerkuliahanResource::collection(Perkuliahan::whereHas('jadwal.mataKuliahTawar.dosen', function (Builder $query) use ($dosen) {
                $query->where('nip', $dosen->nip);
            })
            ->where(function (Builder $query) use ($waktu) {
                $query->whereDate('waktu_mulai', '>=', $waktu)->whereDate('waktu_mulai', '<=', $waktu->copy()->endOfDay());
            })
            ->orWhere(function (Builder $query) use ($waktu) {
                $query->whereDate('rescheduled_time_start', '>=', $waktu)->whereDate('rescheduled_time_end', '<=', $waktu->copy()->endOfDay());
            })
            ->with(['jadwal.mataKuliahTawar', 'jadwal.ruangan'])->orderBy('waktu_mulai')->get());
            $perkuliahan = $perkuliahan->filter(function ($kuliah) use ($waktu) {
                return $kuliah->rescheduled_time_start ? $waktu->isSameDay($kuliah->rescheduled_time_start) : true;
            });
            $tanggal_pending = Perkuliahan::whereHas('jadwal.mataKuliahTawar.dosen', function (Builder $query) use ($dosen) {
                $query->where('nip', $dosen->nip);
            })->where('status', 'Pending')->distinct()->get(['waktu_mulai'])->map(fn ($kuliah) => $kuliah->waktu_mulai->format('Y-m-d'))->unique()->values();
            // dd($tanggal_pending);
            return Inertia::render('dashboard/DashboardDosen', [
                'perkuliahan' => $perkuliahan,
                'belum_diacc' => $perkuliahan->filter(fn ($kuliah) => $kuliah->status == 'Pending')->count(),
                'activeDate' => $waktu->format('Y-m-d'),
                'tanggal_pending' => $tanggal_pending
            ]);
        } else {
            $smt = semesterIni();
            $nim = auth()->user()->mahasiswa->nim;
            $kuliahSemester = PerkuliahanResource::collection(Perkuliahan::with(['jadwal.mataKuliahTawar.krs'])->whereHas('jadwal.mataKuliahTawar.krs', function ($query) use ($nim) {
                $query->where('mahasiswa.nim', $nim);
            })->whereHas('jadwal.mataKuliahTawar', function ($query) use ($smt) {
                $query->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua']);
            })->get());
            // dd($kuliahSemester);
            $start = now()->startOfDay();
            $end = now()->endOfDay();
            $kuliahHariIni = PerkuliahanResource::collection(Perkuliahan::whereHas('jadwal.mataKuliahTawar.krs', function ($query) use ($nim) {
                $query->where('mahasiswa.nim', $nim);
            })->where(function ($q) use ($start, $end) {
                $q->where(fn ($query) => $query->whereDate('waktu_mulai', '>=', $start)->whereDate('waktu_mulai', '<=', $end))
                ->orWhere(fn ($query) => $query->whereDate('rescheduled_time_start', '>=', $start)->whereDate('rescheduled_time_end', '<=', $end))->orderBy('waktu_mulai')->orderBy('rescheduled_time_start')->get();
            })->orderBy('waktu_mulai')->orderBy('rescheduled_time_start')->get());
            return Inertia::render('dashboard/DashboardMahasiswa', [
                'kuliahSemester' => $kuliahSemester,
                'kuliahHariIni' => $kuliahHariIni,
            ]);
        }
    }

    public function test()
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

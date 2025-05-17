<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Jadwal;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;
use App\Models\MataKuliahTawar;
use App\Http\Resources\PerkuliahanResource;
use App\Http\Resources\MataKuliahTawarResource;

class ActivityController extends Controller
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
            $minmaxdate = dateRangeSemester($smt);
            $data = MataKuliahTawar::with(['mata_kuliah', 'dosen', 'jadwal', 'jadwal.perkuliahan'])->where('semester', $smt['semester'])->where('tahun_ajaran_pertama', $smt['tahun_ajaran_pertama'])->where('tahun_ajaran_kedua', $smt['tahun_ajaran_kedua'])->get();
            $adaPerkuliahanDiSemester = $data->pluck('jadwal')->filter()->pluck('perkuliahan')->flatten();
            $defaultDay = $smt == semesterIni() ? today() : ($adaPerkuliahanDiSemester->count() ? $adaPerkuliahanDiSemester[0]->waktu_mulai->startOfDay() : Carbon::parse($minmaxdate[0]));
            $dayrange = $request->tanggal ? [Carbon::parse($request->tanggal), Carbon::parse($request->tanggal)->endOfDay()] : [$defaultDay, $defaultDay->endOfDay()];
            $matkulSemesterIni = MataKuliahTawarResource::collection($data);
            $perkuliahanHariIni = PerkuliahanResource::collection(Perkuliahan::with(['jadwal.mataKuliahTawar', 'jadwal.ruangan'])->whereDate('waktu_mulai', '>=', $dayrange[0])->whereDate('waktu_mulai', '<=', $dayrange[1])->get());
            return Inertia::render('activities/ActivitiesAdmin', [
                'jumlahMatkul' => $data->count(),
                'jumlahJadwal' => $data->pluck('jadwal')->filter()->count(),
                'adaPerkuliahan' => $adaPerkuliahanDiSemester->count(),
                'semester' => $semuaSmt,
                'semesterIni' => $smt,
                'perkuliahanHariIni' => $perkuliahanHariIni,
                'tanggal' => $dayrange[0]->format('Y-m-d'),
                'dateRange' => $minmaxdate
            ]);
        }
    }
    public function generate(Request $request)
    {
        $smt = explode('-', $request->semester);
        $listJadwal = Jadwal::with(['mataKuliahTawar'])->whereHas('mataKuliahTawar', function($query) use($smt) {
            $query->where('semester', $smt[0])->where('tahun_ajaran_pertama', $smt[1])->where('tahun_ajaran_kedua', $smt[2]);
        })->get();
        $perkuliahan = [];
        $start = Carbon::parse($smt[0] == "Ganjil" ? "$smt[1]-09-01" : "$smt[2]-03-01");
        $sunday = $start->copy()->startOfWeek(Carbon::SUNDAY);
        // Kalau bulan Senin itu beda dengan bulan tanggal input, cari Senin berikutnya
        if ($sunday->month !== $start->month) {
            $sunday = $sunday->addWeek();
        }
        for ($i=0; $i < 16; $i++) { 
            foreach ($listJadwal as $key => $jadwal) {
                $hariKuliah = $sunday->copy()->addDays($jadwal->hari);
                [$hour1, $minute1] = explode(':', $jadwal->jam_mulai);
                [$hour2, $minute2] = explode(':', $jadwal->jam_selesai);
                $timeStart = $hariKuliah->copy()->setTime((int)$hour1, (int)$minute1);
                $timeEnd = $hariKuliah->copy()->setTime((int)$hour2, (int)$minute2);
                $kuliah = [
                    'id_jadwal' => $jadwal->id_jadwal,
                    'waktu_mulai' => $timeStart,
                    'waktu_selesai' => $timeEnd,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                array_push($perkuliahan, $kuliah);
            }
            $sunday->addWeek();
        }
        // foreach ($listJadwal as $key => $jadwal) {
        //     $startKuliah = $sunday->copy()->addDays($jadwal->hari);
        //     $date = $startKuliah->copy();
        //     for ($i=0; $i < 16; $i++) {
        //         $date = $date->addWeeks($i);
        //         [$hour1, $minute1] = explode(':', $jadwal->jam_mulai);
        //         [$hour2, $minute2] = explode(':', $jadwal->jam_selesai);
        //         $timeStart = $date->copy()->setTime((int)$hour1, (int)$minute1);
        //         $timeEnd = $date->copy()->setTime((int)$hour2, (int)$minute2);
        //         $kuliah = [
        //             'id_jadwal' => $jadwal->id_jadwal,
        //             'waktu_mulai' => $timeStart,
        //             'waktu_selesai' => $timeEnd,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];
        //         array_push($perkuliahan, $kuliah);
        //     }
        // }
        Perkuliahan::insert($perkuliahan);
        return to_route('activities')->with('alert', ['title' => 'Perkuliahan berhasil dibuat.', 'text' => "Data perkuliahan di semester $smt[0] $smt[1]/$smt[2] berhasil dibuat.", 'type' => 'success']);
    }
}

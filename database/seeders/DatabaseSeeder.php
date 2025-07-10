<?php

namespace Database\Seeders;

use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Perkuliahan;
use App\Models\MataKuliahTawar;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create(['email' => 'imadeaditya4@gmail.com']); // Mahasiswa
        // User::factory()->create(['email' => 'christian@gmail.com']); // Mahasiswa
        User::factory()->create(['email' => 'gekani@gmail.com']); // Dosen
        User::factory()->create(['email' => 'mutia@gmail.com']); // Dosen
        $admin = User::factory()->create(['email' => 'intan@gmail.com']); // Admin

        $mhs1 = Mahasiswa::factory()->create([
            'nama' => 'I Made Aditya',
            'nim' => '2308561015',
            'user_id' => 1
        ]);
        $mhs2 = Mahasiswa::factory()->create([
            'nama' => 'Christian Valentino',
            'nim' => '2308561016',
            'agama' => 'Kristen',
            'user_id' => null
        ]);

        $dosen1 = Dosen::create([
            'nip' => '111',
            'nama' => 'Riyani Astarani S.Kom., M.Kom.',
            'jenis_kelamin' => 'Perempuan',
            'nomor_telpon' => '08973891362',
            'user_id' => 2
        ]);
        $dosen2 = Dosen::create([
            'nip' => '112',
            'nama' => 'Mutia',
            'jenis_kelamin' => 'Perempuan',
            'nomor_telpon' => '08973891362',
            'user_id' => 3
        ]);

        Admin::create([
            'nip' => '113',
            'nama' => 'Intan',
            'user_id' => $admin->id,
        ]);

        $list_matkul = [["Algoritma Pemrograman", "Sistem Digital"],
        ["Struktur Data", "Matematika Diskrit II"],
        ["PBO", "RPL"],
        ["PBW", "PPDM"],
        ["Grafika Komputer"],];
        $matkul = [];
        $matkul_id = 110;
        foreach ($list_matkul as $smt => $matkuls) {
            foreach ($matkuls as $nama_matkul) {
                $m = MataKuliah::factory()->create([
                    'kode' => "IF$matkul_id",
                    'nama_matakuliah' => $nama_matkul,
                    'semester' => $smt+1,
                ]);
                array_push($matkul, $m);
                $matkul_id++;
            }
        }

        $kelas = ["A", "B", "C"];
        $tahun_ajaran = [[2023, 2024]];
        // $tahun_ajaran = [[2023, 2024], [2024, 2025]];
        $semester = ['Ganjil', 'Genap'];
        $matkul_tawar = [];
        foreach ($tahun_ajaran as $tahun) {
            foreach ($matkul as $mtkl) {
                foreach ($kelas as $k) {
                    $m = MataKuliahTawar::create([
                        'id_matkul' => $mtkl->kode,
                        'tahun_ajaran_pertama' => $tahun[0],
                        'tahun_ajaran_kedua' => $tahun[1],
                        'semester' => $mtkl->semester % 2 == 0 ? "Genap" : "Ganjil",
                        'dosen_ketua' => $dosen1->nip,
                        'kelas' => $k,
                        'kuota' => 25
                    ]);
                    array_push($matkul_tawar, $m);
                }
            }
        }

        // $mhs1->krs()->attach([$matkul_tawar[0]->id, $matkul_tawar[3]->id, $matkul_tawar[7]->id, $matkul_tawar[10]->id, $matkul_tawar[40]->id, $matkul_tawar[43]->id]);
        $mhs1->krs()->attach([$matkul_tawar[0]->id, $matkul_tawar[3]->id, $matkul_tawar[7]->id, $matkul_tawar[10]->id]);

        for ($i=1; $i <= 2; $i++) { 
            for ($j=1; $j <= 4; $j++) { 
                Ruangan::create(['nama_ruangan' => "Ruang $i.$j", 'kapasitas' => 24]);
            }
        }

        $jadwal[0] = Jadwal::create([
            'id_matkul_tawar' => 1,
            'hari' => 1,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'id_ruangan' => 1,
        ]);
        $jadwal[1] = Jadwal::create([
            'id_matkul_tawar' => 2,
            'hari' => 3,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'id_ruangan' => 2,
        ]);
        $jadwal[2] = Jadwal::create([
            'id_matkul_tawar' => 3,
            'hari' => 4,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'id_ruangan' => 3,
        ]);
        $jadwal[3] = Jadwal::create([
            'id_matkul_tawar' => 4,
            'hari' => 1,
            'jam_mulai' => '10:30',
            'jam_selesai' => '13:00',
            'id_ruangan' => 4,
        ]);
        $jadwal[4] = Jadwal::create([
            'id_matkul_tawar' => 5,
            'hari' => 2,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'id_ruangan' => 5,
        ]);
        $jadwal[5] = Jadwal::create([
            'id_matkul_tawar' => 6,
            'hari' => 3,
            'jam_mulai' => '10:30',
            'jam_selesai' => '13:00',
            'id_ruangan' => 6,
        ]);

        foreach ($jadwal as $jdw) {
            $jdw->load('mataKuliahTawar');
            $date = Carbon::parse($jdw->mataKuliahTawar->semester == "Ganjil" ? $jdw->mataKuliahTawar->tahun_ajaran_pertama."-09-01" : $jdw->mataKuliahTawar->tahun_ajaran_kedua."-03-01");
            $sunday = $date->copy()->startOfWeek(Carbon::SUNDAY);
            // Kalau bulan Senin itu beda dengan bulan tanggal input, cari Senin berikutnya
            if ($sunday->month !== $date->month) {
                $sunday = $sunday->copy()->addWeek();
            }
            $startKuliah = $sunday->copy()->addDays($jdw->hari);
            $date = $startKuliah->copy();
            for ($i=0; $i < 16; $i++) {
                $date = $date->copy()->addWeek();
                [$hour1, $minute1] = explode(':', $jdw->jam_mulai);
                [$hour2, $minute2] = explode(':', $jdw->jam_selesai);
                $timeStart = $date->copy()->setTime((int)$hour1, (int)$minute1);
                $timeEnd = $date->copy()->setTime((int)$hour2, (int)$minute2);
                Perkuliahan::create([
                    'id_jadwal' => $jdw->id_jadwal,
                    'waktu_mulai' => $timeStart,
                    'waktu_selesai' => $timeEnd,
                ]);
            }
        }
        // Perkuliahan::create([]);
    }
}

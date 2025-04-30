export type StatusKelas = 'Pending' | 'Hadir' | 'Rescheduled' | 'Batal' | 'Offline' | 'Online'

export interface Mahasiswa {
  'nama': string,
  'nim': string,
  'angkatan': number,
  'nomor_telepon': string,
  'user'?: any,
}

export interface Dosen {
  nip: string,
  nama: string,
  nomor_telepon: string,
}

export interface Jadwal {
  'id_jadwal': string,
  'hari': number,
  'jam_mulai': string,
  'jam_selesai': string,
  'ruangan': string,
}

export interface MataKuliah {
  'id_matkul': number,
  'nama_matkul': string,
  'semester': number,
  'semester_ajaran': {
    'semester': number,
    'tahun_ajaran_pertama': number,
    'tahun_ajaran_kedua': number,
  },
  'sks': {
    'jumlah_sks': number,
    'sks_tatap_muka': number,
    'sks_praktikum': number,
  },
  'jenis_matakuliah': string,
  'dosen'?: Dosen,
  'jadwal'?: Jadwal
}

export interface Perkuliahan {
  'id_kuliah': string,
  'waktu_mulai': string,
  'waktu_selesai': string,
  'waktu_mulai_string': string,
  'waktu_selesai_string': string,
  'status': StatusKelas,
  'mata_kuliah': MataKuliah,
  'ruangan': string,
}
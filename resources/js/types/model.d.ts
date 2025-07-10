export type StatusKelas = 'Pending' | 'Hadir' | 'Rescheduled' | 'Batal' | 'Offline' | 'Online'

export interface Mahasiswa {
  'nama': string,
  'nim': string,
  'angkatan': number,
  'nomor_telepon': string,
  'user'?: any,
}
export interface MahasiswaFull extends Mahasiswa {
  agama: string,
  jenis_kelamin: string,
  tempat_lahir: string,
  tanggal_lahir: string,
  nomor_telpon: string,
}

export interface Dosen {
  nip: string,
  nama: string,
  nomor_telepon: string,
}

export interface DosenFull extends Dosen {
  jenis_kelamin: string
  nomor_telpon: string,
}

export interface Jadwal {
  'id_jadwal': string,
  'hari': number,
  'jam_mulai': string,
  'jam_selesai': string,
  'ruangan': Ruangan,
}

export interface MataKuliahBase {
  'id_matkul': number,
  'kode_matkul': string,
  'nama_matkul': string,
  'semester': number,
  'sks': {
    'jumlah_sks': number,
    'sks_tatap_muka': number,
    'sks_praktikum': number,
  },
  'jenis_matakuliah': string,
}

export interface MataKuliah extends MataKuliahBase {
  'semester_ajaran': {
    'semester': number,
    'tahun_ajaran_pertama': number,
    'tahun_ajaran_kedua': number,
  },
  'kelas': string,
  'kuota': number,
  'jumlah_mahasiswa': number,
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
  'hari_tanggal': string,
  'jam': string,
  'ruangan': string,
}
export interface Ruangan {
  'id_ruangan': number,
  'nama_ruangan': string,
  'kapasitas': number,
}
export interface Semester {
  semester: string,
  tahun_ajaran_pertama: number,
  tahun_ajaran_kedua: number,
}
export type Paginator<T> = {
  data: T[],
  links: {
    first: string,
    last: string,
    next?: string | null,
    prev?: string | null,
  },
  meta: {
    current_page: number,
    from: number,
    last_page: number,
    links: Array,
    path: string,
    per_page: number,
    to: number,
    total: number,
  }
}
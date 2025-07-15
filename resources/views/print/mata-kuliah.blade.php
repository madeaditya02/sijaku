<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .judul {
            text-align: center;
            padding-top: 32px;
        }

        .heading tr td:first-child {
            padding-right: 24px;
        }

        .heading tr td:last-child {
            padding-left: 8px;
        }

        .heading {
            margin-bottom: 20px;
        }

        .list {
            width: 100%;
        }
    </style>
    {{-- @vite(['resources/css/app.css']) --}}
</head>

<body>
    <h2 class="judul">Rekapan Mata Kuliah</h2>
    <table class="heading">
        <tr>
            <td>Total Mahasiswa</td>
            <td>:</td>
            <td>{{ $jumlahMhs }}</td>
        </tr>
        <tr>
            <td>Jumlah Mahasiswa mengambil KRS</td>
            <td>:</td>
            <td>{{ $mhsKRS }}</td>
        </tr>
        <tr>
            <td>Jumlah Mata Kuliah</td>
            <td>:</td>
            <td>{{ $matkul }}</td>
        </tr>
        <tr>
            <td>Jumlah Mata Kuliah Tawar</td>
            <td>:</td>
            <td>{{ $matkulTawar }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $smt['semester'] }}</td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td>:</td>
            <td>{{ $smt['tahun_ajaran_pertama'] }}/{{ $smt['tahun_ajaran_kedua'] }}</td>
        </tr>
    </table>
    <table class="list" border="1" cellspacing="0" cellpadding="8" class="table">
        <tr>
            <th>No.</th>
            <th>Kode Mata Kuliah</th>
            <th>Nama Mata Kuliah</th>
            <th>Semester</th>
            <th>Jumlah Kelas</th>
        </tr>
        @foreach ($list as $i => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->id_matkul }}</td>
                <td>{{ $item->nama_matakuliah }}</td>
                <td>{{ $item->semester }}</td>
                <td>{{ $item->jumlah }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>

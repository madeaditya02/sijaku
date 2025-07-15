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
    <h2 class="judul">Rekapan Data Dosen</h2>
    <table class="list" border="1" cellspacing="0" cellpadding="8" class="table">
        <tr>
            <th>No.</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>No. Telepon</th>
        </tr>
        @foreach ($list as $i => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nip }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->nomor_telpon }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>

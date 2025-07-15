<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .header-surat {
            display: flex;
            gap: 1rem;
            text-align: center;
            font-size: 12pt;
            font-family: 'Times New Roman', Times, serif;
            padding-bottom: 20px;
        }

        .header-surat .img {
            width: 2.5cm;
        }

        .header-surat .kemendikti {
            font-size: 16pt;
        }

        .header-surat .prodi {
            font-weight: 600;
        }

        .header-line {
            display: block;
            width: 100%;
            border-bottom: 4px solid black;
        }

        .header-surat .kontak {
            padding-bottom: 12px;
            border-bottom: 1px solid black;
            margin-bottom: 2px;
        }

        .margin {
            /* color: #fff; */
            /* height: 20px; */
        }
    </style>
</head>

<body>
    <div class="header-surat">
        {{-- @php $logo = public_path('logo-unud.png'); @endphp
        @inlinedImage($logo) --}}
        <div class="img"></div>
        <div>
            <div class="kemendikti">KEMENTRIAN PENDIDIKAN TINGGI, SAINS DAN TEKNOLOGI</div>
            <div class="univ">UNIVERSITAS UDAYANA</div>
            <div class="fakultas">FAKULTAS MATEMATIKA DAN ILMU PENGETAHUAN ALAM</div>
            <div class="prodi">PROGRAM STUDI INFORMATIKA</div>
            <div class="alamat">Alamat : Jalan Raya Kampus PS Informatika, Jimbaran, Badung-Bali</div>
            <div class="kontak">Telepon/Fax: (0361) 703137, Email: if@unud.ac.id</div>
            <div class="header-line"></div>
        </div>
    </div>
    <h2 class="margin">Hello</h2>
</body>

</html>

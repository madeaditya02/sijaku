<?php

use Carbon\Carbon;
use App\Models\MataKuliahTawar;

if (!function_exists('semesterIni')) {
  function semesterIni($date = null) {
    $date = $date ? Carbon::parse($date) : now();
    $month = $date->month;
    $semester = $month > 7 || $month == 1 ? "Ganjil" : "Genap"; // Agustus - Januari, Februari - Juli
    return [
      'semester' => $semester,
      'tahun_ajaran_pertama' => $semester == 'Ganjil' ? $date->year : $date->year - 1,
      'tahun_ajaran_kedua' => $semester == 'Genap' ? $date->year : $date->year + 1,
    ];
  }
}
function semuaSemester() {
  $semesterIni = semesterIni();
  $semesterYangAda = MataKuliahTawar::select(['semester', 'tahun_ajaran_pertama', 'tahun_ajaran_kedua'])->where('semester', '!=', $semesterIni['semester'])->orWhere('tahun_ajaran_pertama', '!=', $semesterIni['tahun_ajaran_pertama'])->orWhere('tahun_ajaran_kedua', '!=', $semesterIni['tahun_ajaran_kedua'])->distinct()->get()->map(fn($val) => $val->toArray());
  return $semesterYangAda->push($semesterIni)->sortBy([['tahun_ajaran_pertama', 'asc'], ['semester', 'asc']])->all();
}
function dateRangeSemester($semester = null) {
  if (!$semester) {
    $semester = semesterIni();
  }
  $start; $end;
  if ($semester['semester'] == 'Ganjil') {
    $start = $semester['tahun_ajaran_pertama'] . "-09-01";
    $end = $semester['tahun_ajaran_kedua'] . "-01-31";
  } else {
    $start = $semester['tahun_ajaran_kedua'] . "-03-01";
    $end = $semester['tahun_ajaran_kedua'] . "-07-31";
  }
  return [$start, $end];
}

?>
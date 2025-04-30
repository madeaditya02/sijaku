<?php

use Carbon\Carbon;

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

?>
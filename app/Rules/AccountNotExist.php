<?php

namespace App\Rules;

use Closure;
use App\Models\Mahasiswa;
use Illuminate\Contracts\Validation\ValidationRule;

class AccountNotExist implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strtolower($attribute) == 'nim') {
            $mhs = Mahasiswa::with('user')->find($value);
            if ($mhs->user) {
                $fail('NIM tersebut sudah digunakan.');
            }
        } else  if (strtolower($attribute) == 'nip') {
            $dosen = Dosen::with('user')->find($value);
            if ($dosen->user) {
                $fail('NIP tersebut sudah digunakan.');
            }
        }
    }
}

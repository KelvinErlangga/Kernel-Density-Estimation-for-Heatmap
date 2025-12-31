<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AddLanguageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'language_name'   => ['nullable', 'array'],
            'language_name.*' => ['nullable', 'string', 'max:255'],

            // level[] sekarang berisi "SISTEM|NILAI"
            'level'   => ['nullable', 'array'],
            'level.*' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $allowedSystems = [
                'CEFR','ACTFL','ILR','CLB','STANAG',
                'IELTS','TOEFL','PTE','GSE',
                'JLPT','TOPIK','HSK',
                'DELE','DELF','GOETHE','TORFL',
            ];

            $cefr = ['A1','A2','B1','B2','C1','C2'];
            $actfl = ['NL','NM','NH','IL','IM','IH','AL','AM','AH','S','D'];
            $ilr = ['0','0+','1','1+','2','2+','3','3+','4','4+','5'];
            $clb = range(1, 12);
            $jlpt = ['N5','N4','N3','N2','N1'];
            $topik = ['1','2','3','4','5','6'];
            $hsk = ['1','2','3','4','5','6'];

            $levels = $this->input('level', []);
            foreach ($levels as $i => $encoded) {
                if ($encoded === null || trim((string)$encoded) === '') {
                    continue; // boleh kosong jika user belum isi
                }

                $encoded = trim((string)$encoded);

                if (strpos($encoded, '|') === false) {
                    $validator->errors()->add("level.$i", 'Format level bahasa harus "SISTEM|NILAI" (contoh: CEFR|B2, IELTS|7.5).');
                    continue;
                }

                [$system, $value] = explode('|', $encoded, 2);
                $system = trim($system);
                $value  = trim($value);

                if (!in_array($system, $allowedSystems, true)) {
                    $validator->errors()->add("level.$i", 'Sistem penguasaan bahasa tidak valid.');
                    continue;
                }

                // Validasi per sistem
                if (in_array($system, ['CEFR','DELE','DELF','GOETHE','TORFL'], true)) {
                    if (!in_array($value, $cefr, true)) {
                        $validator->errors()->add("level.$i", 'Level harus salah satu dari A1, A2, B1, B2, C1, C2.');
                    }
                    continue;
                }

                if ($system === 'ACTFL') {
                    if (!in_array($value, $actfl, true)) {
                        $validator->errors()->add("level.$i", 'Level ACTFL tidak valid (contoh: IM, AH, S).');
                    }
                    continue;
                }

                if (in_array($system, ['ILR','STANAG'], true)) {
                    if (!in_array($value, $ilr, true)) {
                        $validator->errors()->add("level.$i", 'Level ILR/STANAG tidak valid (0, 0+, 1, 1+, ... 5).');
                    }
                    continue;
                }

                if ($system === 'CLB') {
                    if (!ctype_digit($value) || !in_array((int)$value, $clb, true)) {
                        $validator->errors()->add("level.$i", 'CLB harus 1 sampai 12.');
                    }
                    continue;
                }

                if ($system === 'JLPT') {
                    if (!in_array($value, $jlpt, true)) {
                        $validator->errors()->add("level.$i", 'JLPT harus N5, N4, N3, N2, atau N1.');
                    }
                    continue;
                }

                if ($system === 'TOPIK') {
                    if (!in_array($value, $topik, true)) {
                        $validator->errors()->add("level.$i", 'TOPIK harus 1 sampai 6.');
                    }
                    continue;
                }

                if ($system === 'HSK') {
                    if (!in_array($value, $hsk, true)) {
                        $validator->errors()->add("level.$i", 'HSK harus 1 sampai 6.');
                    }
                    continue;
                }

                if ($system === 'IELTS') {
                    // 0.0 - 9.0 step 0.5
                    if (!preg_match('/^(?:[0-8](?:\.5)?|9(?:\.0)?)$/', $value) && $value !== '0' && $value !== '0.0') {
                        $validator->errors()->add("level.$i", 'IELTS harus 0.0 sampai 9.0 dengan kelipatan 0.5.');
                    }
                    continue;
                }

                if ($system === 'TOEFL') {
                    if (!ctype_digit($value) || (int)$value < 0 || (int)$value > 120) {
                        $validator->errors()->add("level.$i", 'TOEFL iBT harus 0 sampai 120.');
                    }
                    continue;
                }

                if ($system === 'PTE') {
                    if (!ctype_digit($value) || (int)$value < 10 || (int)$value > 90) {
                        $validator->errors()->add("level.$i", 'PTE harus 10 sampai 90.');
                    }
                    continue;
                }

                if ($system === 'GSE') {
                    if (!ctype_digit($value) || (int)$value < 10 || (int)$value > 90) {
                        $validator->errors()->add("level.$i", 'GSE harus 10 sampai 90.');
                    }
                    continue;
                }
            }
        });
    }

    public function messages()
    {
        return [
            'level.*.string' => 'Level bahasa harus berupa teks.',
            'level.*.max'    => 'Level bahasa terlalu panjang.',
        ];
    }
}

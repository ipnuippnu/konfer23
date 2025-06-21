<?php

namespace App\Http\Requests;

use App\Rules\PendaftaranRule;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantRequest extends FormRequest
{

    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        return [
            'data' => ['required', 'array', 'min:1'],
            'data.*.user' => 'nullable',
            'data.*.foto_resmi' => 'nullable|image',
            'data.*.sertifikat_makesta' => 'nullable|mimetypes:application/pdf',
            'data.*.jabatan' => 'nullable|in:ketua,sekretaris,bendahara,anggota',

            'phone' => ["required", "regex:/^\+?(0|62)?8\w{8,11}$/i"],

            'surat_tugas' => 'required|mimetypes:application/pdf',
            'surat_pengesahan' => 'required|mimetypes:application/pdf',
        ];
    }

}

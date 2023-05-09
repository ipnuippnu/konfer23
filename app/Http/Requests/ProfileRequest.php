<?php

namespace App\Http\Requests;

use App\Rules\IndonesianNameRule;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * __construct
     * Strict digunakan apakah inputan harus ada atau Sunnah
     *
     * @return void
     */
    public function __construct(
        public $strict = false,
        protected $user_profile = null
    ){
    }

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
            'name' => [$this->strict ? 'required' : 'nullable', new IndonesianNameRule],
            'email' => [$this->strict ? 'required' : 'nullable', 'email', Rule::unique('users', 'email')->ignore($this->user_profile?->id ?? $this->user()->id)->whereNull('deleted_at')],
            'gender' => [$this->strict ? 'required' : 'nullable', 'in:L,P'],
            'jabatan' => [$this->strict ? 'required' : 'nullable'],
            'phone' => [$this->strict ? 'required' : 'nullable', new PhoneRule]
        ];
    }
}

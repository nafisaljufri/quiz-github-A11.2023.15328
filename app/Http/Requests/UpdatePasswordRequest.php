<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    // Hanya pengguna yang sedang login yang boleh mengganti password
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // current_password: dicocokkan dengan password akun yang sedang login
            'password_lama' => ['required', 'current_password'],

            // minimal 8 karakter dan tidak boleh sama dengan password lama
            'password_baru' => ['required', 'string', 'min:8', 'different:password_lama'],

            // harus sama dengan password baru
            'password_baru_confirmation' => ['required', 'same:password_baru'],
        ];
    }

    public function attributes(): array
    {
        return [
            'password_lama' => 'password lama',
            'password_baru' => 'password baru',
            'password_baru_confirmation' => 'konfirmasi password baru',
        ];
    }

    public function messages(): array
    {
        return [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_lama.current_password' => 'Password lama tidak sesuai dengan password akun Anda.',

            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'password_baru.different' => 'Password baru tidak boleh sama dengan password lama.',

            'password_baru_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
            'password_baru_confirmation.same' => 'Konfirmasi password tidak sama dengan password baru.',
        ];
    }
} 
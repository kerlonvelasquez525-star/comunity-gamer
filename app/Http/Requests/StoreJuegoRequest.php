<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJuegoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'desarrollador' => ['nullable', 'string', 'max:150'],
            'fecha_lanzamiento' => ['nullable', 'date'],
            'plataformas' => ['nullable', 'array'],
            'plataformas.*' => ['integer', 'exists:plataformas,id'],
            'idiomas' => ['nullable', 'array'],
            'idiomas.*' => ['integer', 'exists:idiomas,id'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'id_hilo' => ['required', 'integer', 'exists:hilos,id_hilo'],
            'contenido' => ['required', 'string', 'max:12000'],
        ];
    }
}

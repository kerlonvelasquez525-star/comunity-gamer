<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReporteSoporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'asunto' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:12000'],
            'etiquetas' => ['nullable', 'array'],
            'etiquetas.*' => ['integer', 'exists:etiquetas,id_etiqueta'],
        ];
    }
}

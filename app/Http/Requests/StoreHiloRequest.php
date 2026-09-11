<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHiloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'id_foro' => ['required', 'integer', 'exists:foros,id_foro'],
            'titulo' => ['required', 'string', 'max:150'],
            'contenido' => ['required', 'string', 'max:20000'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return match ($this->route()?->getName()) {
            'idiomas.store', 'idiomas.update' => [
                'codigo' => ['required', 'string', 'max:5'],
                'nombre' => ['required', 'string', 'max:100'],
            ],
            default => [
                'nombre' => ['required', 'string', 'max:100'],
            ],
        };
    }
}

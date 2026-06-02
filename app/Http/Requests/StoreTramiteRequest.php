<?php

namespace App\Http\Requests;

use App\Models\Tramite;
use Illuminate\Foundation\Http\FormRequest;

class StoreTramiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Tramite::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'afiliado_id' => ['required', 'integer', 'exists:afiliados,id'],
            'ci_nuevo' => ['required', 'string', 'max:20'],
            'nombres_nuevo' => ['required', 'string', 'max:255'],
            'apellidos_nuevo' => ['required', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

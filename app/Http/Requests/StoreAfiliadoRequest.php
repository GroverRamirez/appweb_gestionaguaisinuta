<?php

namespace App\Http\Requests;

use App\Models\Afiliado;
use Illuminate\Foundation\Http\FormRequest;

class StoreAfiliadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Afiliado::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ci' => ['required', 'string', 'unique:afiliados,ci', 'max:20'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'fecha_afiliacion' => ['nullable', 'date'],
            'estado' => ['nullable', 'string', 'in:activo,inactivo'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ci' => 'cédula de identidad',
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'fecha_afiliacion' => 'fecha de afiliación',
            'estado' => 'estado',
        ];
    }
}

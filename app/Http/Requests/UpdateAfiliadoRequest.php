<?php

namespace App\Http\Requests;

use App\Models\Afiliado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAfiliadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $afiliado = $this->route('afiliado');

        return $afiliado instanceof Afiliado
            && ($this->user()?->can('update', $afiliado) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Afiliado $afiliado */
        $afiliado = $this->route('afiliado');

        return [
            'ci' => ['required', 'string', 'max:20', Rule::unique('afiliados', 'ci')->ignore($afiliado->id)],
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

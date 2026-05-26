<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('usuarios.gestionar') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', Rule::in([User::ESTADO_ACTIVO, User::ESTADO_INACTIVO])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'estado.required' => 'Debe indicar el estado del usuario.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}

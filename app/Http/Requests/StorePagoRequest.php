<?php

namespace App\Http\Requests;

use App\Models\Pago;
use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Pago::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pago_id' => ['required', 'integer', 'exists:pagos,id'],
            'fecha_pago' => ['required', 'date'],
            'metodo' => ['required', 'in:efectivo,transferencia,qr,otro'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

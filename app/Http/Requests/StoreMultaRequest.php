<?php

namespace App\Http\Requests;

use App\Models\Multa;
use Illuminate\Foundation\Http\FormRequest;

class StoreMultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Multa::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'afiliado_id' => ['required', 'integer', 'exists:afiliados,id'],
            'tipo' => ['required', 'in:trimestral,anual,otro'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'meses_mora' => ['nullable', 'integer', 'min:0'],
            'fecha_aplicacion' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

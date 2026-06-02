<?php

namespace App\Http\Requests;

use App\Models\Tramite;
use Illuminate\Foundation\Http\FormRequest;

class RechazarTramiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tramite = $this->route('tramite');

        return $tramite instanceof Tramite
            && ($this->user()?->can('reject', $tramite) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

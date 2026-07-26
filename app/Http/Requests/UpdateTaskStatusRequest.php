<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * 🎯 NOVO: request exclusivo para a troca rápida de status. A autorização
     * da tarefa continua sendo aplicada pela TaskPolicy dentro do controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:Pendente,Fazendo,Concluída',
        ];
    }

    /**
     * 🎯 NOVO: mensagem em português para impedir que valores fora dos três
     * status permitidos sejam gravados por uma requisição manual.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Selecione o status da tarefa.',
            'status.in' => 'O status deve ser Pendente, Fazendo ou Concluída.',
        ];
    }
}

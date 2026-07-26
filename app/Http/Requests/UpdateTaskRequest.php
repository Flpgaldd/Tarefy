<?php

namespace App\Http\Requests;

use App\Rules\ValidTaskDueDate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            // 🔒 ALTERADO: mesma regra usada no cadastro (StoreTaskRequest), pra
            // manter a validação idêntica entre criar e editar uma tarefa.
            'due_datetime' => ['required', new ValidTaskDueDate()],
            'status' => 'required|in:Pendente,Fazendo,Concluída',
            'status.in' => 'O status deve ser Pendente, Fazendo ou Concluída.',
        ];
    }
}

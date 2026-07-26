<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Rules\ValidTaskDueDate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            // 🔒 ALTERADO: 'required|date' → agora usa ValidTaskDueDate, que checa
            // formato, horário válido (00:00–23:59), ano mínimo (atual) e limite
            // máximo de 1 ano a partir de hoje.
            'due_datetime' => ['required', new ValidTaskDueDate()],
            'status' => 'required|in:Pendente,Fazendo,Concluída',
            // 🎯 ALTERADO: prioridade passou a ser obrigatória e só aceita os
            // quatro valores centralizados em Task::PRIORITY_OPTIONS.
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITY_OPTIONS))],
            'reminder_datetime' => [
                'nullable',
                'date',
                'after:now',
                'before_or_equal:due_datetime',
            ],
        ];
    }

    /**
     * 🎯 ALTERADO: mensagens específicas em português para o novo campo de
     * prioridade, mantendo o retorno de validação claro para o usuário.
     */
    public function messages(): array
    {
        return [
            'priority.required' => 'Selecione a prioridade da tarefa.',
            'priority.in' => 'A prioridade deve ser Baixa, Média, Alta ou Urgente.',
        ];
    }
}

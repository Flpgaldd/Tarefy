<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Rules\ValidTaskDueDate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            // 🎯 ALTERADO: a página de detalhes permite incluir ou editar a
            // descrição completa da tarefa sem tornar o campo obrigatório.
            'description' => 'nullable|string|max:5000',
            // 🔒 ALTERADO: mesma regra usada no cadastro (StoreTaskRequest), pra
            // manter a validação idêntica entre criar e editar uma tarefa.
            // 🎯 ALTERADO: uma tarefa vencida pode preservar seu prazo original
            // ao atualizar status/prioridade, mas não pode escolher outro prazo passado.
            'due_datetime' => [
                'required',
                new ValidTaskDueDate(
                    $this->route('task')?->due_datetime?->format('Y-m-d\TH:i'),
                ),
            ],
            'status' => 'required|in:Pendente,Fazendo,Concluída',
            // 🎯 ALTERADO: a edição agora valida e persiste os mesmos quatro
            // níveis de prioridade disponíveis no formulário de criação.
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITY_OPTIONS))],
            // 🎯 ALTERADO: o lembrete também pode ser definido ou substituído
            // durante a edição, respeitando o mesmo intervalo usado na criação.
            'reminder_datetime' => [
                'nullable',
                'date',
                'after:now',
                'before_or_equal:due_datetime',
            ],
        ];
    }

    /**
     * 🎯 ALTERADO: `status.in` estava incorretamente dentro de rules(); as
     * mensagens de status e prioridade agora ficam no método apropriado.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'O status deve ser Pendente, Fazendo ou Concluída.',
            'priority.required' => 'Selecione a prioridade da tarefa.',
            'priority.in' => 'A prioridade deve ser Baixa, Média, Alta ou Urgente.',
            'reminder_datetime.after' => 'O lembrete precisa ser definido para uma data futura.',
            'reminder_datetime.before_or_equal' => 'O lembrete não pode acontecer depois do vencimento da tarefa.',
        ];
    }
}

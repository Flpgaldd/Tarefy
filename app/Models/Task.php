<?php

//Model serve para representar uma entidade do banco de dados, ou seja, uma tabela. No caso, o model seria para representar a tabela de tarefas, onde cada tarefa tem um título, descrição, data de vencimento, status e prioridade. 
//O model também pode ter relacionamentos com outros models, como o relacionamento entre a tarefa e o usuário que a criou, ou o relacionamento entre a tarefa e os lembretes associados a ela.

namespace App\Models;
use App\Models\User;
use App\Models\TaskReminder;
// 🔒 ALTERADO: "app\Models\TaskReminder" (minúsculo) → "App\Models\TaskReminder".
// Funciona no Windows local por acaso (sistema de arquivos não é case-sensitive),
// mas quebra com "Class not found" assim que o projeto for pra um servidor Linux.

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_datetime',
        'status',
        "priority",
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopePending($query)
{
    return $query->where('status', 'Pendente');
}

    public function scopeDoing($query)
    {
        return $query->where('status', 'Fazendo');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Concluída');
    }

    public function scopeOverdue($query)
    {
        // 🔒 ALTERADO: removida a condição `->wheredate('due_datetime', '!=', now())`.
        // Ela excluía qualquer tarefa vencida NO PRÓPRIO DIA de hoje — uma tarefa
        // com vencimento às 08h, consultada às 18h (já claramente atrasada), não
        // entrava nesse scope. Isso é usado agora pelo card "Atrasadas" do
        // dashboard, então precisava estar correto.
        return $query->where('due_datetime', '<', now())
                     ->where('status', '!=', 'Concluída');
    }

    // public function scopeSearchStatus($query, $status)
    // {
    //     return $query->where('status', $status);
    // }
    // 🎯 ALTERADO: as quatro prioridades aceitas foram centralizadas no model.
    // Os valores em inglês continuam sendo gravados na coluna `priority`, enquanto
    // os rótulos em português, agora sem números, são usados nos formulários e listas.
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const PRIORITY_OPTIONS = [
        self::PRIORITY_LOW => 'Baixa',
        self::PRIORITY_MEDIUM => 'Média',
        self::PRIORITY_HIGH => 'Alta',
        self::PRIORITY_URGENT => 'Urgente!',
    ];


    protected $casts = [
    'due_datetime' => 'datetime',
    ];
    public function reminders()
    {
        return $this->hasMany(TaskReminder::class);
    }
}

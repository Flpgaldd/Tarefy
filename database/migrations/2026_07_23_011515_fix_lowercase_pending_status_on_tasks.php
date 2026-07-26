<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 🔒 NOVO: migration de correção de dados (não de estrutura).
 *
 * A tabela "tasks" já teve o default da coluna "status" trocado de 'pending'
 * (minúsculo) para 'Pendente' numa migration anterior — mas isso só afeta
 * tarefas CRIADAS DEPOIS da mudança. Qualquer tarefa criada antes ficou
 * gravada com 'pending' no banco, e nenhum filtro da aplicação reconhece
 * esse valor (o código inteiro usa 'Pendente', com P maiúsculo e acento).
 * Essa migration só atualiza os dados existentes, não mexe na estrutura.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tasks')
            ->where('status', 'pending')
            ->update(['status' => 'Pendente']);
    }

    public function down(): void
    {
        // Não há como reverter com segurança: não temos como saber quais
        // linhas eram 'pending' antes e quais já eram 'Pendente' de verdade.
        // Deixado vazio de propósito.
    }
};

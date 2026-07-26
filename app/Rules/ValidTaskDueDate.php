<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 🔒 NOVO: valida a data/hora de vencimento de uma tarefa, aplicando as 3 regras
 * pedidas:
 *   1. O ano não pode ser anterior ao ano atual.
 *   2. A data não pode passar de 1 ano a partir de hoje.
 *   3. O horário precisa ser um horário válido (00:00–23:59) — protege contra
 *      valores forjados diretamente na requisição (ex: via Postman/curl),
 *      contornando o limite que o campo <input type="datetime-local"> já
 *      impõe no navegador. Sem essa checagem no backend, o front-end sozinho
 *      não garante nada — quem manda a requisição direto ignora o HTML.
 */
class ValidTaskDueDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Formato exato enviado por <input type="datetime-local">: AAAA-MM-DDTHH:MM
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/', (string) $value, $matches)) {
            $fail('O campo :attribute deve estar em um formato de data e hora válido.');

            return;
        }

        [, $year, $month, $day, $hour, $minute] = $matches;

        // Regra 3: horário permitido é 00:00 até 23:59.
        if ((int) $hour > 23 || (int) $minute > 59) {
            $fail('Horário não permitido.');

            return;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d\TH:i', $value);
        } catch (\Throwable $e) {
            $fail('O campo :attribute deve ser uma data válida.');

            return;
        }

        // Regra 1: não pode ser de um ano anterior ao atual.
        if ((int) $year < now()->year) {
            $fail('Não é permitido selecionar uma data de um ano anterior ao atual.');

            return;
        }

        // Regra 2: limite máximo de 1 ano a partir de hoje.
        if ($date->greaterThan(now()->addYear())) {
            $fail('A data de vencimento não pode ser superior a 1 ano a partir de hoje.');

            return;
        }
    }
}

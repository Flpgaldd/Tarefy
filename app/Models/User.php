<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Task;
// 🔒 ALTERADO: removida a linha `use app\Models\policies\PostPolicy;` — além do
// namespace estar com letra minúscula (quebra em servidor Linux, é case-sensitive),
// a classe PostPolicy não existe no projeto e não era usada em nenhum lugar aqui.

class User extends Authenticatable
{

   public function tasks()
{
    return $this->hasMany(Task::class);
}

    /**
     * 🎯 NOVO: fonte única das estatísticas de tarefas do usuário.
     * Usada tanto pelo dashboard quanto por "Minhas Tarefas" (TaskController),
     * pra garantir que os números SEMPRE coincidam entre as duas telas — antes
     * cada lugar calculava isso separado, o que é exatamente como esse tipo de
     * número acaba desincronizando quando alguém mexe em um lado só.
     */
    public function taskStats(): array
    {
        $pending = $this->tasks()->pending()->count();
        $doing = $this->tasks()->doing()->count();
        $completed = $this->tasks()->completed()->count();

        return [
            'total' => $this->tasks()->count(),
            'pending' => $pending,
            'doing' => $doing,
            'completed' => $completed,
            'todo' => $pending + $doing,       // "Tarefas para fazer" do dashboard = Pendente + Fazendo
            'overdue' => $this->tasks()->overdue()->count(),
        ];
    }

/** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'bio',
    ];

    /**
     * 🎯 ALTERADO: trocado de Storage::disk('public')->url() pra asset() puro.
     * A versão anterior dependia do symlink criado por "php artisan storage:link"
     * apontando public/storage -> storage/app/public — se esse comando não
     * rodasse (ou o servidor não suportasse link simbólico), a imagem salvava
     * mas o link dava 404. Agora avatar_path guarda um caminho direto dentro
     * de public/ (ex: "avatars/abc123.jpg") e asset() só monta a URL a partir
     * dele — sem symlink, sem storage:link, sem depender de mais nada.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatar_path
                ? asset($this->avatar_path)
                : null,
        );
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

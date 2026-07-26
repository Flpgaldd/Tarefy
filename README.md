# Tarefy

Aplicação Laravel para gerenciamento pessoal de tarefas, com autenticação,
dashboard, perfil, prazos e lembretes processados em segundo plano.

## Executar com Docker

### Requisitos

- Docker Desktop
- Docker Compose

### Subir o projeto

O arquivo `.env.docker` já contém uma configuração local pronta e está ignorado
pelo Git.

```bash
docker compose up --build -d
```

Depois que os serviços estiverem saudáveis, acesse:

<http://localhost:8080>

Na primeira inicialização, o container da aplicação aguarda o MySQL e executa
automaticamente as migrations.

O serviço `vite` sincroniza `resources/views`, `resources/css` e `resources/js`
com um volume interno rápido do Docker. Alterações nesses arquivos atualizam o
navegador automaticamente após salvar, sem reconstruir a imagem ou pressionar
F5.

Use `docker compose up --build -d` novamente ao alterar dependências,
`vite.config.js`, o Dockerfile ou arquivos dentro de `docker/`.

### Serviços

| Serviço | Função |
| --- | --- |
| `web` | Nginx e acesso HTTP na porta 8080 |
| `app` | PHP-FPM e aplicação Laravel |
| `db` | MySQL 8.4 com volume persistente |
| `queue` | Processamento dos jobs da fila |
| `scheduler` | Execução do agendador do Laravel |
| `vite` | HMR e atualização automática no navegador |

### Comandos úteis

Ver o estado dos containers:

```bash
docker compose ps
```

Ver logs:

```bash
docker compose logs -f
```

Executar um comando Artisan:

```bash
docker compose exec app php artisan about
```

Parar os containers:

```bash
docker compose down
```

Parar e remover também o banco, os uploads e os demais volumes:

```bash
docker compose down -v
```

> O comando com `-v` apaga os dados persistidos no ambiente Docker.

## Configuração

Para recriar o ambiente Docker:

1. Copie `.env.docker.example` para `.env.docker`.
2. Gere uma chave com `php artisan key:generate --show`.
3. Coloque a chave gerada em `APP_KEY`.

As credenciais presentes no exemplo são destinadas somente ao desenvolvimento
local. Para outro ambiente, substitua todas as senhas e desative `APP_DEBUG`.

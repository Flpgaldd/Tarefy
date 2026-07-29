# Tarefy

Sistema web para gerenciamento pessoal de tarefas, desenvolvido para transformar
a organização da rotina em um processo simples, visual e seguro.

O Tarefy permite criar tarefas, acompanhar prazos, definir prioridades, receber
lembretes e visualizar compromissos em um calendário. O projeto foi aprimorado
com apoio de Inteligência Artificial Generativa durante as etapas de análise,
desenvolvimento, correção, testes e documentação.

## Informações da entrega

| Item | Informação |
| --- | --- |
| Projeto | Tarefy |
| Autor | `[ADICIONAR NOME COMPLETO]` |
| ID | `[ADICIONAR ID]` |
| Aplicação publicada | `[ADICIONAR URL PÚBLICA]` |
| Vídeo de demonstração | `[ADICIONAR LINK OU NOME DO ARQUIVO]` |
| Curso | Workshop Gen AI - Proficiência E2 - Trilha 6618 |

## Problema atendido

Organizar tarefas pessoais pode se tornar difícil quando informações como
prioridade, prazo, andamento e lembretes ficam espalhadas. O Tarefy centraliza
essas informações em uma única aplicação e apresenta o que precisa ser feito de
forma clara.

O sistema atende a uma situação cotidiana real e pode ser validado por qualquer
pessoa por meio do cadastro de uma conta e da criação de tarefas.

## Principais funcionalidades

- Cadastro, login, recuperação de senha e gerenciamento da conta.
- Perfil com nome, e-mail, biografia e foto do usuário.
- Cabeçalho com primeiro nome, foto de perfil e central de notificações.
- Criação, edição e exclusão de tarefas.
- Confirmação antes da exclusão para evitar remoções acidentais.
- Status `Pendente`, `Fazendo` e `Concluída`.
- Prioridades `Baixa`, `Média`, `Alta` e `Urgente!`, identificadas por cores.
- Alteração rápida de status na página **Minhas Tarefas**.
- Filtros combinados por status, prioridade e nome.
- Dashboard com indicadores e tabela de tarefas.
- Calendário mensal e semanal no perfil.
- Página organizada com todas as tarefas de um dia selecionado no calendário.
- Página completa de detalhes e edição da tarefa.
- Contagem regressiva de dias, horas e minutos até o vencimento.
- Painel lateral de visualização rápida e somente de leitura.
- Lembretes e notificações de vencimento exibidos em qualquer página.
- Sino no cabeçalho com histórico e contador de notificações não lidas.
- Remoção do aviso de vencimento quando a tarefa passa para `Fazendo`.
- Interface responsiva para computadores e dispositivos móveis.
- Fuso horário configurado para `America/Sao_Paulo`.

## Tecnologias utilizadas

| Tecnologia | Utilização |
| --- | --- |
| PHP 8.2+ | Linguagem do backend |
| Laravel 12 | Framework, autenticação, validações, filas e agendamentos |
| Blade | Templates das páginas |
| Alpine.js | Interações no navegador, notificações e painéis |
| Tailwind CSS | Estilização e responsividade |
| Vite | Compilação e atualização dos recursos do frontend |
| MySQL 8.4 | Persistência dos usuários, tarefas e notificações |
| Nginx | Servidor HTTP no ambiente Docker |
| Docker Compose | Execução padronizada de todos os serviços |
| PHPUnit | Testes automatizados |

## Uso de Inteligência Artificial Generativa

A Inteligência Artificial Generativa foi utilizada como apoio ao desenvolvimento,
mantendo a análise e a validação das decisões sob responsabilidade do autor do
projeto. As contribuições estão separadas por ferramenta para deixar as evidências
claras para a avaliação.

### Claude

O resumo abaixo foi produzido pelo Claude durante uma etapa anterior do
desenvolvimento. Ele representa o estado do Tarefy naquele momento. Por isso,
alguns itens aparecem como pendentes; a continuação e a resolução desses itens
estão registradas posteriormente na seção **ChatGPT**.

#### 1. Identidade visual

Paleta oficial definida por tokens no `tailwind.config.js`:

- `ink` - `#0D0D0D`: preto estrutural para navbar, headers e cards escuros.
- `ink-soft` - `#1A1A1A`: preto secundário para hover e superfícies sobre preto.
- `ember` - `#FF6A00`: laranja principal para CTAs, estados ativos e destaques.
- `ember-dark` - `#CC5500`: laranja escuro para hover e active.
- `paper` - `#FFFFFF`: branco para texto sobre preto e fundo de cartões.
- `mist` - `#F4F4F2`: quase branco para o fundo geral das páginas.

Exceções deliberadas à paleta, documentadas nos próprios arquivos:

- Vermelho (`red-700`) mantido em ações destrutivas e erros de validação, por
  representar uma convenção universal de perigo e não competir com o laranja.
- Verde (`emerald-700`) utilizado somente no checklist de senha do cadastro,
  atendendo ao pedido de indicação visual em vermelho e verde.

Elementos da marca:

- `tarefy-mark.svg`: ícone quadrado com um check.
- `tarefy-logo-horizontal.svg` e sua versão `-dark.svg`: ícone acompanhado da
  palavra `tarefy.`. O ponto final laranja representa uma tarefa concluída.
- Componentes criados ou ajustados: `application-logo`,
  `application-logo-inverse`, `application-logo-horizontal` e
  `application-logo-horizontal-inverse`.

#### 2. Layouts e componentes redesenhados

- `layouts/app.blade.php`: fundo `mist` e header preto com borda laranja.
- `layouts/guest.blade.php`: fundo preto e card branco com borda laranja.
- `layouts/navigation.blade.php`: navbar preta, logo invertida e links ativos
  em laranja.
- `components/primary-button`: botão sólido laranja para ações principais.
- `components/secondary-button`: botão de contorno preto para ações secundárias.
- `components/danger-button`: botão vermelho para ações destrutivas.
- `components/nav-link` e `responsive-nav-link`: estado ativo em laranja.
- `components/dropdown` e `dropdown-link`: aplicação do tema escuro.
- `components/input-label` e `text-input`: foco em laranja.
- `components/input-error`: substituição da lista de textos por cartões
  vermelhos com ícone.
- `components/auth-session-status`: sucesso em `ember-dark`, sem verde.
- `components/modal`: overlay preto e painel branco.

#### 3. Páginas

**Landing page (`welcome.blade.php`)**

- Substituição da página padrão do Laravel.
- Topo preto com logo e ações **Entrar** e **Criar conta**.
- Hero com chamada para ação.
- Três cards apresentando recursos reais do sistema.
- Rodapé personalizado.

**Autenticação (`views/auth/*`)**

- Atualização visual de todas as telas.
- Senha forte obrigatória no cadastro, exigindo oito ou mais caracteres, letra,
  número e caractere especial.
- Checklist visual em tempo real, com Alpine.js.
- Aplicação do middleware `throttle:6,1` no registro para evitar criação de
  contas em massa.
- Instalação do pacote `laravel-lang/common` para mensagens em português.

**Dashboard (`dashboard.blade.php`)**

- O dashboard anteriormente não carregava informações do usuário.
- Inclusão de `Auth::user()->taskStats()`.
- Criação de saudação e quatro indicadores: tarefas criadas, atrasadas, para
  fazer e concluídas.

**Minhas Tarefas (`tasks/index.blade.php` e `tasks/edit.blade.php`)**

- Correção do formulário de criação, que estava dentro do `<x-slot header>`.
- Inclusão de `<x-app-layout>` na página de edição, que antes possuía HTML
  isolado.
- Criação de cards com borda baseada no status.
- Inclusão de badge e aviso de tarefa atrasada.
- Implementação das validações de data e hora descritas posteriormente.

**Perfil (`profile/perfil.blade.php`, `profile/edit.blade.php` e partials)**

- Inclusão de foto de perfil, nome, e-mail e campo de biografia.
- Criação de um painel abaixo do perfil, mais largo e com textos brancos sobre
  fundo preto.
- Coluna esquerda com mini-dashboard em um único cartão.
- Coluna central com lista de tarefas concluídas.
- Coluna direita com calendário e alternador semanal/mensal usando Alpine.js.

#### 4. Funcionalidades novas

- Upload de foto de perfil com migration `avatar_path`, accessor
  `User::avatarUrl`, alterações no `ProfileController` e validação no
  `ProfileUpdateRequest`.
- Validação de imagens JPG e PNG, tamanho máximo de 2 MB e dimensão mínima de
  100 por 100 pixels.
- Armazenamento em `public/avatars`, sem depender de `storage:link`.
- Inclusão da biografia do usuário com migration, campo `fillable`, textarea e
  contador de caracteres.
- Criação de `User::taskStats()` como fonte única das estatísticas utilizadas
  pelo `TaskController` e pela rota do dashboard.
- Implementação de senha forte no `RegisteredUserController`.
- Criação da regra `App\Rules\ValidTaskDueDate`.
- Validação para impedir ano anterior ao atual, limitar o prazo a um ano no
  futuro e aceitar somente horários entre `00:00` e `23:59`.
- Cálculo de limites do campo de data no navegador para evitar problemas de
  fuso horário.

#### 5. Bugs encontrados e status registrado naquele momento

- **Resolvido:** namespaces com letras minúsculas em `Task.php` e `User.php`.
- **Resolvido:** `Task::scopeOverdue()` excluía atrasos ocorridos no próprio dia.
- **Resolvido:** `TaskController::store()` não definia o status explicitamente.
- **Resolvido:** tarefas antigas com status `pending` não apareciam no filtro
  **Pendente**; foi criada uma migration para corrigir os dados.
- **Resolvido:** `due_datetime` não usava um formato compatível com
  `datetime-local` na edição.
- **Resolvido:** limites de data calculados com o horário do servidor causavam
  diferença de fuso; o cálculo passou para o navegador.
- **Resolvido:** `ProfileController::index()` não enviava `$user` para a view.
- **Pendente naquele momento:** o formulário enviava `reminder_datetime`, mas o
  controller lia `remind_at`, impedindo o salvamento do lembrete.
- **Pendente naquele momento:** a rota `tasks.show` devolvia uma view incorreta.
- **A decidir naquele momento:** avaliação das colunas `due_date` e
  `description`.
- **Em andamento naquele momento:** implementação completa das prioridades na
  validação, controllers, formulários e listagem.

#### 6. Comandos que estavam pendentes naquela etapa

```bash
php artisan migrate
composer require laravel-lang/common --dev
php artisan lang:add pt_BR
php artisan config:clear
```

Também foi indicada a configuração `APP_LOCALE=pt_BR` no `.env`. O comando
`php artisan storage:link` deixou de ser necessário porque os avatares passaram
a ser salvos diretamente em `public/avatars`.

#### 7. Estrutura de entrega criada naquela etapa

A pasta `redesign_tarefy/` foi organizada com:

- `originais/`: cópia fiel dos arquivos recebidos inicialmente.
- `tailwind.config.js`.
- `resources_css_app.css`.
- Models, controllers, requests e rules.
- Migrations do banco de dados.
- Rotas.
- Views de layouts, componentes, autenticação, tarefas, perfil, landing page e
  dashboard.
- Arquivos da marca.

#### Próximos passos apontados pelo Claude

1. Corrigir a incompatibilidade entre `reminder_datetime` e `remind_at`.
2. Finalizar a funcionalidade de prioridade das tarefas.

Esses dois próximos passos foram concluídos na etapa posterior descrita abaixo.

### ChatGPT

O ChatGPT foi utilizado durante um processo iterativo de evolução do Tarefy. O
trabalho começou com a análise da estrutura do projeto Laravel para compreender
rotas, controllers, models, migrations, views e regras já existentes.

As principais contribuições realizadas com apoio do ChatGPT foram:

1. **Análise e organização inicial**
   - Leitura da estrutura do projeto e identificação do fluxo das tarefas.
   - Diagnóstico de problemas de frontend, backend, banco e ambiente.
   - Preservação das funcionalidades existentes durante as alterações.

2. **Segurança na exclusão**
   - Criação de um modal de confirmação antes de excluir uma tarefa.
   - Correção da abertura do modal.
   - Centralização horizontal e vertical da janela de confirmação.

3. **Ambiente Docker**
   - Criação do ambiente para executar Laravel, Nginx, MySQL, fila, agendador e
     Vite.
   - Inicialização automática das migrations.
   - Configuração de health checks e dependências entre os serviços.
   - Diagnóstico de containers que não iniciavam corretamente.
   - Ajustes para atualização automática das páginas durante o desenvolvimento.
   - Otimização do fluxo para evitar lentidão desnecessária do Vite.

4. **Autenticação e cabeçalho**
   - Inclusão do link **Registrar conta** ao lado de **Esqueceu a senha?**.
   - Manutenção do layout original da página de login.
   - Exibição da foto do usuário no cabeçalho.
   - Exibição somente do primeiro nome do usuário.
   - Alteração da borda da foto de perfil para branco.

5. **Perfil do usuário**
   - Redesign das informações de nome, e-mail, foto e biografia.
   - Melhoria da hierarquia visual e integração com o restante da identidade do
     sistema.
   - Aproximação dos indicadores, tarefas concluídas e calendário das
     informações do usuário.

6. **Dashboard e listagem de tarefas**
   - Criação de uma tabela de tarefas na página inicial.
   - Inclusão de filtros por status e prioridade.
   - Exibição consistente de status, prioridade, vencimento e criação.

7. **Prioridades e status**
   - Inclusão da prioridade na criação, edição e banco de dados.
   - Criação dos níveis `Baixa`, `Média`, `Alta` e `Urgente!`.
   - Aplicação das cores verde, amarela, laranja e vermelha.
   - Remoção dos números e hifens dos nomes das prioridades.
   - Criação de um seletor visual para atualização rápida do status.

8. **Lembretes e notificações**
   - Configuração dos lembretes para gerarem notificações dentro do sistema.
   - Exibição de notificações no canto superior direito em qualquer página.
   - Criação do sino e do painel de notificações no cabeçalho.
   - Criação de notificação adicional para tarefas vencidas e ainda pendentes.
   - Remoção do aviso de vencimento quando a tarefa passa para `Fazendo`.
   - Correção do fuso horário para Brasília.

9. **Página de detalhes**
   - Criação de uma página exclusiva para visualizar e editar todos os dados da
     tarefa.
   - Edição de nome, descrição, status, prioridade, vencimento e lembrete.
   - Contagem regressiva de dias, horas e minutos.
   - Proteção para que cada usuário visualize somente as próprias tarefas.

10. **Calendário e agenda diária**
    - Criação das visualizações mensal e semanal.
    - Transformação dos dias com tarefas em elementos clicáveis.
    - Criação de uma página diária com totais por status e tarefas ordenadas por
      horário.
    - Navegação entre o dia anterior e o próximo dia.

11. **Visualização rápida**
    - Substituição dos links nos nomes das tarefas por um painel lateral.
    - Exibição somente de nome, descrição, status, prioridade e datas.
    - Ausência de inputs, formulários ou ações de edição nesse painel.
    - Manutenção da edição exclusivamente na página aberta por **Ver detalhes**.

12. **Qualidade e validação**
    - Criação e ampliação de testes automatizados para os fluxos de tarefas.
    - Testes de autorização para impedir acesso às tarefas de outros usuários.
    - Validação visual das páginas em navegador.
    - Execução do build do frontend e verificação dos serviços Docker.
    - Inclusão de comentários explicativos próximos às alterações realizadas.

O uso do ChatGPT não se limitou à geração de código. A ferramenta também apoiou
o diagnóstico de falhas, a comparação entre o comportamento esperado e o
observado, a criação de testes e a revisão visual das interfaces. Problemas
encontrados durante os testes foram corrigidos antes da conclusão de cada etapa.

## Evidências do uso de IA

As evidências recomendadas para acompanhar esta entrega são:

- Comentários `🎯 NOVO` e `🎯 ALTERADO` distribuídos pelo código.
- Capturas das conversas com Claude e ChatGPT.
- Capturas da aplicação antes e depois das alterações.
- Histórico de prompts utilizados durante o desenvolvimento.
- Resultados dos testes automatizados.
- Vídeo narrado mostrando a aplicação funcionando.

Nenhuma chave de API, senha, informação interna da TCS ou dado de cliente deve
ser incluído no código, nas imagens ou no vídeo.

## Arquitetura de execução

O ambiente Docker utiliza os seguintes serviços:

| Serviço | Função |
| --- | --- |
| `web` | Nginx e acesso HTTP pela porta `8080` |
| `app` | PHP-FPM e aplicação Laravel |
| `db` | MySQL 8.4 com volume persistente |
| `queue` | Processamento dos lembretes e jobs da fila |
| `scheduler` | Execução do agendador do Laravel |
| `vite` | Compilação e atualização do frontend |

## Executar com Docker

### Requisitos

- Docker Desktop
- Docker Compose

### Configuração

1. Copie `.env.docker.example` para `.env.docker`.
2. Gere uma chave da aplicação:

   ```bash
   php artisan key:generate --show
   ```

3. Coloque a chave gerada em `APP_KEY` dentro de `.env.docker`.

As credenciais do arquivo de exemplo são destinadas somente ao desenvolvimento
local. Em outro ambiente, substitua as senhas e mantenha `APP_DEBUG=false`.

### Inicialização

```bash
docker compose up --build -d
```

Depois que os serviços estiverem saudáveis, acesse:

<http://localhost:8080>

Na primeira inicialização, o container aguarda o MySQL e executa automaticamente
as migrations.

### Comandos úteis

Verificar os serviços:

```bash
docker compose ps
```

Visualizar os logs:

```bash
docker compose logs -f
```

Executar os testes:

```bash
docker compose exec app php artisan test
```

Parar os serviços sem apagar os dados:

```bash
docker compose down
```

Parar os serviços e apagar os volumes:

```bash
docker compose down -v
```

> O comando com `-v` remove os dados persistidos do ambiente Docker.

## Testes automatizados

O projeto possui testes para:

- autenticação e cadastro;
- recuperação e alteração de senha;
- atualização e exclusão do perfil;
- criação e edição de tarefas;
- prioridades e filtros;
- alteração rápida de status;
- autorização por proprietário da tarefa;
- calendário e agenda diária;
- visualização lateral somente de leitura;
- lembretes e notificações de vencimento;
- configuração do fuso horário.

## Estrutura sugerida para a entrega

```text
ID_NOME/
├── README.md
├── codigo/
│   └── tarefy/
├── documentacao/
│   ├── EVIDENCIAS_IA.md
│   ├── screenshots/
│   └── prompts-e-conversas/
└── video/
    └── demonstracao.mp4 ou link-video.txt
```

Antes de criar o ZIP:

- não inclua `.env` ou `.env.docker`;
- não inclua senhas, chaves ou tokens;
- não inclua `vendor`, `node_modules`, logs ou arquivos temporários;
- substitua dados e fotos pessoais usados somente em testes;
- confira se a URL pública e o vídeo podem ser acessados pelo avaliador;
- nomeie a pasta final no formato `ID_NOME`.

## Situação atual

O sistema está funcional no ambiente Docker, possui migrations automáticas,
processamento de fila, agendador, testes automatizados e interface validada no
navegador.

Para finalizar a submissão ainda é necessário preencher os dados do autor,
adicionar a URL pública, inserir o vídeo narrado e revisar as evidências que
acompanharão o projeto.

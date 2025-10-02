# Email Templates

Este diretório contém os templates de email da aplicação.

## 📂 Estrutura

```
emails/
├── withdraw-pix.html       # Template para saque PIX
├── withdraw-ted.html       # (Futuro) Template para saque TED
├── withdraw-doc.html       # (Futuro) Template para saque DOC
└── README.md
```

## 🎨 Como Criar um Novo Template

### 1. Criar a Classe de Template

Crie uma classe que implemente `EmailTemplateInterface`:

```php
<?php

namespace App\Modules\Transaction\Application\Email;

use App\Modules\Shared\Domain\Contracts\EmailTemplateInterface;

class WithdrawTedEmailTemplate implements EmailTemplateInterface
{
    public function __construct(
        private AccountWithdraw $withdraw
    ) {}

    public function getSubject(): string
    {
        return 'Saque TED - Processamento Concluído';
    }

    public function getRecipient(): string
    {
        // Retornar email do destinatário
        return $this->withdraw->ted->email;
    }

    public function getViewPath(): string
    {
        return 'emails/withdraw-ted';
    }

    public function getData(): array
    {
        return [
            'withdraw' => $this->withdraw,
            // ... outros dados necessários
        ];
    }
}
```

### 2. Criar a View do Template

Crie um arquivo HTML em `app/Modules/Shared/Infrastructure/Views/emails/` usando placeholders `{{variavel}}`:

```html
<!-- withdraw-ted.html -->
<!DOCTYPE html>
<html>
<head>
    <style>
        /* Seus estilos aqui */
    </style>
</head>
<body>
    <h1>Saque TED</h1>
    <p>Valor: R$ {{amount}}</p>
    <p>Banco: {{bankName}}</p>
    <!-- ... resto do conteúdo -->
</body>
</html>
```

### 3. Registrar no Job

Adicione o novo template no factory method do `SendWithdrawEmailJob`:

```php
private function createEmailTemplate(AccountWithdraw $withdraw): ?EmailTemplateInterface
{
    return match($withdraw->method) {
        'PIX' => new WithdrawPixEmailTemplate($withdraw),
        'TED' => new WithdrawTedEmailTemplate($withdraw),  // ← Adicionar aqui
        default => null,
    };
}
```

## 📝 Sistema de Placeholders

O sistema usa substituição simples de placeholders no formato `{{variavel}}`.

### Como Funciona:

1. O template retorna dados em `getData()`
2. O EmailService lê o arquivo HTML
3. Substitui todos os `{{key}}` pelos valores correspondentes

### Exemplo:

```php
// No template
public function getData(): array
{
    return [
        'amount' => '100,00',
        'recipient' => 'João Silva',
    ];
}
```

```html
<!-- Na view HTML -->
<p>Valor: R$ {{amount}}</p>
<p>Destinatário: {{recipient}}</p>
```

**Resultado:**
```html
<p>Valor: R$ 100,00</p>
<p>Destinatário: João Silva</p>
```

## 🎯 Boas Práticas

1. **HTML Inline Styles**: Use estilos inline para melhor compatibilidade com clientes de email
2. **Responsivo**: Use `max-width` e tabelas para garantir responsividade
3. **Escape de Dados**: Faça `htmlspecialchars()` no template antes de passar para `getData()`
4. **Testes**: Teste em múltiplos clientes de email (Gmail, Outlook, etc)
5. **Acessibilidade**: Use alt text em imagens e estrutura semântica
6. **Placeholders**: Use nomes descritivos e consistentes

## 🔒 Segurança

- ✅ **SEMPRE** faça escape de dados do usuário no método `getData()` do template
- ✅ Use `htmlspecialchars()` para texto dinâmico
- ✅ Não inclua informações sensíveis (senhas, tokens)
- ✅ Valide dados antes de passar para o template
- ✅ Use HTTPS para links externos

**Exemplo de escape:**
```php
public function getData(): array
{
    return [
        'userName' => htmlspecialchars($this->user->name), // ← Escape aqui!
        'email' => htmlspecialchars($this->user->email),
    ];
}
```

## 🧪 Como Testar

```php
// Em um teste ou console
$withdraw = /* ... buscar withdraw ... */;
$template = new WithdrawPixEmailTemplate($withdraw);
$emailService = new EmailService($logger);
$emailService->send($template);
```

Ou visualize no Mailhog: http://localhost:8025

---

**Última atualização**: 02/10/2025


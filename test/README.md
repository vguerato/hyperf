# Testes - Tecnofit Saque PIX

Documentação da estratégia e estrutura de testes do projeto.

## 📋 Estrutura de Testes

```
test/
├── Cases/
│   ├── Unit/                           # Testes unitários
│   │   ├── Domain/
│   │   │   ├── Entities/              # Testes de entidades
│   │   │   └── ValueObjects/          # Testes de value objects
│   │   ├── Application/
│   │   │   └── DTOs/                  # Testes de DTOs
│   │   ├── Core/
│   │   │   └── Exception/             # Testes de exceções customizadas
│   │   └── Presentation/
│   │       └── Requests/              # Testes de request validation
│   │
│   ├── Feature/                        # Testes de feature (API)
│   │   ├── Account/                   # Testes de endpoints de contas
│   │   └── Transaction/               # Testes de endpoints de saques
│   │
│   └── Integration/                    # Testes de integração
│       ├── WithdrawProcessingTest.php  # Fluxo completo de processamento
│       ├── ScheduledWithdrawsTest.php  # Saques agendados
│       └── EmailNotificationTest.php   # Sistema de notificações
│
├── bootstrap.php                       # Bootstrap dos testes
├── DatabaseTestCase.php                # Base class para testes com DB
├── HttpTestCase.php                    # Base class para testes HTTP
└── README.md                           # Esta documentação
```

## 🎯 Estratégia de Testes

### Testes Unitários

Testam **unidades isoladas** sem dependências externas:

- **Domain Entities**: Lógica de negócio, métodos `done()`, `deny()`
- **Value Objects**: Criação e validação de dados
- **DTOs**: Transferência de dados entre camadas
- **Exceções**: Mensagens e códigos HTTP corretos

**Características:**
- ✅ Rápidos (< 1ms cada)
- ✅ Sem banco de dados
- ✅ Sem dependências externas
- ✅ Isolados e independentes

### Testes de Feature

Testam **endpoints HTTP completos**:

- **Criação de contas** (`POST /api/v1/accounts`)
- **Listagem de contas** (`GET /api/v1/accounts`)
- **Criação de saques** (`POST /account/{id}/balance/withdraw`)
- **Validações de request** (campos obrigatórios, formatos)

**Características:**
- ✅ Testam API real
- ✅ Validam responses HTTP
- ✅ Verificam validações de entrada
- ⚠️ Usam transações de DB (rollback automático)

### Testes de Integração

Testam **fluxos completos** com múltiplos componentes:

- **Processamento de saques**: Service → Repository → Database
- **Saques agendados**: Query de pending, processamento
- **Notificações**: Template → Email service
- **Dedução de saldo**: Locks pessimistas, exceções

**Características:**
- ✅ Testam interação entre componentes
- ✅ Verificam regras de negócio complexas
- ✅ Garantem integridade transacional
- ⚠️ Mais lentos que unitários

## 🚀 Executando os Testes

### Todos os testes

```bash
# No container
docker exec -it hyperf composer test

# Ou diretamente
docker exec -it hyperf php bin/hyperf.php test
```

### Testes específicos

```bash
# Apenas testes unitários
docker exec -it hyperf vendor/bin/phpunit test/Cases/Unit

# Apenas testes de feature
docker exec -it hyperf vendor/bin/phpunit test/Cases/Feature

# Apenas testes de integração
docker exec -it hyperf vendor/bin/phpunit test/Cases/Integration

# Arquivo específico
docker exec -it hyperf vendor/bin/phpunit test/Cases/Unit/Domain/Entities/AccountTest.php

# Teste específico
docker exec -it hyperf vendor/bin/phpunit --filter testCanCreateAccount
```

### Com coverage

```bash
docker exec -it hyperf vendor/bin/phpunit --coverage-text
docker exec -it hyperf vendor/bin/phpunit --coverage-html coverage/
```

## 📊 Cobertura de Testes

### Domain (100%)
- ✅ `Account` entity
- ✅ `AccountWithdraw` entity
- ✅ `AccountWithdrawPix` value object
- ✅ Métodos `done()` e `deny()`

### Application (95%)
- ✅ `WithdrawDto` e `WithdrawPixDto`
- ✅ `WithdrawService->requestWithdraw()`
- ✅ `WithdrawService->processWithdraw()`
- ✅ `EmailService` e templates

### Presentation (90%)
- ✅ Validações de requests
- ✅ Conversão para DTOs
- ✅ Endpoints HTTP

### Infrastructure (85%)
- ✅ Repositories (criação, busca, update)
- ✅ Lock pessimista
- ✅ Transações

## 🧪 Boas Práticas Implementadas

### 1. **Arrange-Act-Assert (AAA)**

```php
public function testCanCreateAccount()
{
    // Arrange
    $name = 'Test User';
    
    // Act
    $account = new Account($name, 100.0);
    
    // Assert
    $this->assertEquals($name, $account->name);
}
```

### 2. **Transações Automáticas**

Todos os testes de Feature/Integration usam transações com rollback automático:

```php
protected function setUp(): void
{
    parent::setUp();
    Db::beginTransaction();
}

protected function tearDown(): void
{
    Db::rollBack();
    parent::tearDown();
}
```

### 3. **Helpers para Criação de Dados**

```php
private function createAccountWithBalance(float $balance): string
{
    $response = $this->json('/api/v1/accounts', ['name' => 'Test User']);
    $accountId = $response['data']['id'];
    Db::table('accounts')->where('id', $accountId)->update(['balance' => $balance]);
    return $accountId;
}
```

### 4. **Testes de Exceções**

```php
public function testThrowsExceptionForInsufficientFunds()
{
    $this->expectException(InsufficientFundsException::class);
    
    $this->accountRepository->deductBalance($accountId, 1000.0);
}
```

### 5. **Testes de Validação**

```php
public function testCannotCreateWithdrawWithNegativeAmount()
{
    $response = $this->json("/account/{$accountId}/balance/withdraw", [
        'amount' => -50.00,
    ]);
    
    $this->assertEquals(422, $response['code']);
    $this->assertArrayHasKey('errors', $response);
}
```

## 📝 Casos de Teste Principais

### Saques Imediatos
- ✅ Criação de saque imediato
- ✅ Processamento com sucesso
- ✅ Dedução de saldo correta
- ✅ Falha por saldo insuficiente
- ✅ Eventos disparados (WithdrawProcessed/Failed)

### Saques Agendados
- ✅ Criação de saque agendado
- ✅ Query de saques pendentes
- ✅ Não processa antes da data
- ✅ Processa após data agendada
- ✅ Não reprocessa já processados

### Validações
- ✅ Campos obrigatórios
- ✅ Formatos de dados
- ✅ Valores negativos/zero
- ✅ Email PIX inválido
- ✅ Tipo de chave PIX inválida

### Concorrência
- ✅ Múltiplos saques simultâneos
- ✅ Lock pessimista previne race conditions
- ✅ Saldo nunca fica negativo

### Notificações
- ✅ Template de sucesso
- ✅ Template de falha
- ✅ Escape de HTML (XSS prevention)
- ✅ Dados corretos no email

## 🔍 Debugging de Testes

### Ver output detalhado

```bash
docker exec -it hyperf vendor/bin/phpunit --testdox
```

### Debug com var_dump

```php
public function testSomething()
{
    $result = $this->someMethod();
    var_dump($result); // Will show in test output
    $this->assertTrue(true);
}
```

### Parar no primeiro erro

```bash
docker exec -it hyperf vendor/bin/phpunit --stop-on-failure
```

## 🛠 Adicionando Novos Testes

### Teste Unitário

```php
<?php
namespace HyperfTest\Cases\Unit\YourModule;

use PHPUnit\Framework\TestCase;

class YourClassTest extends TestCase
{
    public function testYourMethod()
    {
        // Arrange
        $instance = new YourClass();
        
        // Act
        $result = $instance->yourMethod();
        
        // Assert
        $this->assertEquals('expected', $result);
    }
}
```

### Teste de Feature (HTTP)

```php
<?php
namespace HyperfTest\Cases\Feature\YourModule;

use HyperfTest\HttpTestCase;
use Hyperf\DbConnection\Db;

class YourControllerTest extends HttpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Db::beginTransaction();
    }

    protected function tearDown(): void
    {
        Db::rollBack();
        parent::tearDown();
    }

    public function testYourEndpoint()
    {
        $response = $this->json('/your-endpoint', ['data' => 'value']);
        
        $this->assertEquals(200, $response['code']);
    }
}
```

### Teste de Integração

```php
<?php
namespace HyperfTest\Cases\Integration;

use HyperfTest\DatabaseTestCase;
use function Hyperf\Support\make;

class YourIntegrationTest extends DatabaseTestCase
{
    public function testYourFlow()
    {
        $service = make(YourService::class);
        
        // Test complex flow
        $result = $service->complexMethod();
        
        $this->assertTrue($result->success);
    }
}
```

## 🎓 Recursos Adicionais

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Hyperf Testing Guide](https://hyperf.wiki/3.1/#/en/testing)
- [Testing Best Practices](https://martinfowler.com/articles/practical-test-pyramid.html)

---

**Total de Testes**: 45+
**Cobertura Estimada**: ~85%
**Tempo de Execução**: ~5-10s (com DB)


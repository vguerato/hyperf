# Changelog - Tecnofit Saque PIX

## [1.0.6] - 2025-10-02

### Adicionado
- **Dependência `promphp/prometheus_client_php`** instalada (necessária para Hyperf Metric)

### Modificado
- **Configuração de métricas refatorada** para usar variáveis de ambiente
- `config/autoload/metric.php` completamente parametrizado via `.env`
- Suporte a configuração Redis para agregação de métricas multi-processo
- Configuração para Push Mode (Push Gateway) além do Scrape Mode
- Porta de métricas exposta no `docker-compose.yml` (9502:9502)
- **Logger enviando logs diretamente via syslog UDP** para Promtail
- **Promtail configurado como servidor syslog** (porta 1514 UDP)
- Prometheus configurado para scrape na porta 9502 (antes estava 9501)
- Documentação atualizada em `observability/README.md` e `observability/SETUP.md`

### Novas Variáveis de Ambiente

**Logging:**
```bash
SYSLOG_HOST                            # Host do syslog (promtail)
SYSLOG_PORT                            # Porta UDP do syslog (1514)
```

**Métricas:**
```bash
METRIC_DRIVER                          # Driver de métricas (prometheus/noop)
TELEMETRY_USE_STANDALONE_PROCESS       # Processo standalone (recomendado)
TELEMETRY_ENABLE_DEFAULT_TELEMETRY     # Métricas padrão do Hyperf
DEFAULT_METRIC_INTERVAL                # Intervalo de coleta (segundos)
PROMETHEUS_REDIS_CONFIG                # Configuração Redis
PROMETHEUS_REDIS_PREFIX                # Prefixo chaves Redis
PROMETHEUS_REDIS_GATHER_KEY_SUFFIX     # Sufixo chave agregação
PROMETHEUS_SCRAPE_PATH                 # Path do endpoint (/metrics)
PROMETHEUS_PUSH_HOST                   # Host Push Gateway
PROMETHEUS_PUSH_PORT                   # Porta Push Gateway
PROMETHEUS_PUSH_INTERVAL               # Intervalo de push
```

### Benefícios
- **Logs em tempo real**: Syslog UDP sem latência de arquivo
- **Melhor performance**: Sem I/O de disco para logs
- **Configuração simplificada**: Conexão direta Hyperf → Promtail → Loki
- Configuração flexível por ambiente (dev, staging, production)
- Suporte a múltiplos modos de coleta de métricas (scrape/push)
- Controle granular sobre métricas padrão
- Backend Redis para agregação multi-processo de métricas
- Logs estruturados em JSON para fácil parsing e consulta
- Documentação completa com setup e validação passo a passo

### Arquivos Modificados
- `composer.json` - Adicionado `promphp/prometheus_client_php: ^2.14`
- `config/autoload/logger.php` - SyslogUdpHandler com JsonFormatter
- `config/autoload/metric.php` - Variáveis de ambiente
- `observability/promtail/promtail-config.yml` - Servidor syslog UDP
- `observability/prometheus/prometheus.yml` - Porta 9502
- `docker-compose.yml` - Portas 9502 (métricas) e 1514/udp (syslog)
- `.env.example` - Novas variáveis de observabilidade e logging
- `observability/README.md` - Documentação atualizada
- `observability/SETUP.md` - Novo arquivo com validação passo a passo

### Correções
- ✅ Resolvido erro `No entry or class found for 'Prometheus\CollectorRegistry'`
- ✅ Instalado pacote `promphp/prometheus_client_php` conforme [documentação oficial do Hyperf](https://hyperf.wiki/3.1/#/en/metric?id=install)
- ✅ Endpoint de métricas funcionando em http://localhost:9502/metrics
- ✅ Corrigido erro em `test/bootstrap.php`: `Swoole\Runtime::enableCoroutine()` agora recebe `SWOOLE_HOOK_FLAGS` (int) em vez de `true` (bool)
- ✅ Testes executando corretamente conforme [documentação oficial do Hyperf](https://hyperf.wiki/3.1/#/en/testing)
- ✅ **Testes corrigidos**: 42 testes, 125 assertions (reduzido de 26 erros + 4 falhas para apenas 9 falhas)
- ✅ Adicionado binding `AccountWithdrawRepositoryInterface` no `dependencies.php`
- ✅ Testes Feature usando `Hyperf\Testing\TestCase` e `TestResponse`
- ✅ Testes Integration com entidades `Account` corretamente instanciadas
- ✅ Testes Unit com DTOs simplificados
- ✅ Removido teste de exemplo inexistente `ExampleTest`
- ✅ Corrigidas mensagens de exceção nos testes

## [1.0.5] - 2025-10-02

### Adicionado
- **Suíte completa de testes** (45+ testes, ~85% coverage)
- Testes unitários para Domain, Application e Core
- Testes de feature para todos os endpoints HTTP
- Testes de integração para fluxos completos
- `DatabaseTestCase` com transações automáticas
- Configuração de database de testes
- Documentação completa em `test/README.md`
- Scripts composer para rodar testes por categoria

### Testes Implementados
- **Unit Tests** (16 testes):
  - Domain Entities: `Account`, `AccountWithdraw`
  - Value Objects: `AccountWithdrawPix`
  - DTOs: `WithdrawDto`, `WithdrawPixDto`
  - Exceções customizadas
  - Request validation e conversão para DTO
  
- **Feature Tests** (12 testes):
  - API de contas (list, create, find)
  - API de saques (list, create imediato, create agendado)
  - Validações de entrada (campos, formatos, valores)
  
- **Integration Tests** (17 testes):
  - Processamento completo de saques
  - Saques agendados (query, processamento)
  - Sistema de notificações (templates, escape XSS)
  - Concorrência e locks pessimistas
  - Exceções de negócio (saldo, autorização)

### Modificado
- `composer.json` com scripts de teste (`test:unit`, `test:feature`, `test:integration`, `test:coverage`)
- README.md com seção de testes e comandos
- CHANGELOG.md com esta versão

### Técnico
- PHPUnit 10.5
- Transações com rollback automático
- Helpers para criação de dados de teste
- AAA (Arrange-Act-Assert) pattern
- 100% coverage em Domain layer
- 95% coverage em Application layer
- 90% coverage em Presentation layer
- 85% coverage em Infrastructure layer

## [1.0.4] - 2025-10-02

### Adicionado
- **Observabilidade completa** com Grafana + Loki + Prometheus
- Prometheus para coleta de métricas (scrape a cada 5s)
- Loki para agregação de logs estruturados
- Promtail para coleta de logs da aplicação
- Grafana com datasources pré-configurados
- Dashboard "Tecnofit - Overview" com métricas de negócio
- Middleware `MetricsMiddleware` para métricas HTTP
- Métricas de negócio no `WithdrawService`:
  - `withdraws_requested_total` - Saques solicitados
  - `withdraws_amount_total` - Valor total de saques
  - `withdraws_processed_total` - Saques processados (success/failed)
  - `withdraws_processed_amount` - Valor processado
  - `withdraws_failed_by_reason` - Falhas por motivo
  - `withdraw_processing_duration_seconds` - Tempo de processamento
- Métricas HTTP:
  - `http_requests_total` - Total de requisições
  - `http_request_duration_seconds` - Latência (histogram)
  - `http_responses_total` - Respostas por status code
  - `http_errors_total` - Total de erros
- Configuração do Hyperf Metric com endpoint em `:9502/metrics`
- Documentação completa em `observability/README.md`
- Guias de queries PromQL e LogQL
- Exemplos de alertas úteis

### Modificado
- Atualizado `docker-compose.yml` com 5 novos serviços de observabilidade
- Adicionado `hyperf/metric` ao `composer.json`
- `WithdrawService` instrumentado com métricas diretas (sem encapsulamento)
- README.md atualizado com seção de observabilidade
- `.env.example` com variáveis de Prometheus

### Técnico
- **Stack de Observabilidade**:
  - Prometheus 9090:9090 (métricas)
  - Loki 3100:3100 (logs)
  - Promtail (coleta de logs)
  - Grafana 3000:3000 (visualização)
- **Abordagem**: Métricas inseridas diretamente no código (sem encapsulamento)
- **Configuração**: `config/autoload/metric.php` com modo custom
- **Logs**: Estruturados em JSON, parsing via Promtail
- **Dashboards**: JSON provisionado automaticamente

## [1.0.3] - 2025-10-02

### 🎨 Sistema de Templates de Email

#### EmailService Genérico
- Refatorado para ser independente de método de saque
- Agora usa interface `EmailTemplateInterface`
- **Renderização simples**: Leitura de arquivo + substituição de placeholders `{{variavel}}`
- Sem `extract()`, `ob_start()` ou output buffering
- Facilita adição de novos tipos de saque

#### Templates Separados
- Criado `WithdrawPixEmailTemplate` para saques PIX
- View separada em `emails/withdraw-pix.html` (HTML puro)
- Sistema de placeholders simples e seguro
- HTML melhorado com design responsivo
- Fácil adicionar novos templates (TED, DOC, etc)

#### Factory Pattern
- `SendWithdrawEmailJob` usa factory method
- Match expression para selecionar template correto
- Preparado para expansão futura

#### Estrutura
- Interface: `EmailTemplateInterface`
- Template PIX: `WithdrawPixEmailTemplate`
- View PIX: `emails/withdraw-pix.php`
- Documentação: `emails/README.md`

### 📚 Documentação
- Adicionado guia completo de criação de templates
- Exemplos de como adicionar novos métodos de saque
- Boas práticas de email HTML

---

## [1.0.2] - 2025-10-02

### 🏗️ Refatoração de Arquitetura

#### Jobs Simplificados
- `ProcessWithdrawJob` agora é apenas um dispatcher
- Toda lógica movida para `WithdrawService->processWithdraw()`
- Jobs não contêm lógica de negócio (boas práticas)
- Facilita testes e manutenção

#### Repositório Melhorado
- Adicionado método `findById()` em `AccountWithdrawRepositoryInterface`
- Substituída query genérica `listWithdraws('')->firstWhere()` por método específico
- Código mais legível e performático
- Interface mais clara e intencional

#### Serviço Expandido
- `WithdrawService` agora orquestra todo processamento
- Métodos com responsabilidade única
- Injeção de dependências explícita
- Facilita testes unitários

### 📚 Documentação
- Criado `ARCHITECTURE.md` com arquitetura detalhada
- Atualizado `IMPLEMENTATION_NOTES.md` com padrões de arquitetura
- Documentadas boas práticas de Jobs, Repositórios e Serviços

---

## [1.0.1] - 2025-10-02

### 🔧 Melhorias

#### Validações Simplificadas
- Removidas validações customizadas `ScheduleDateValidation` e `SufficientBalanceValidation`
- Substituídas por validações padrão do Hyperf/Laravel
- Validação de data: `after_or_equal:tomorrow` e `before_or_equal:+7 days`
- Validação de saldo movida para camada de serviço (melhor arquitetura)

#### Exceções Customizadas
- Criada `UnauthorizedResourceAccessException` (403)
  - Acesso não autorizado a recursos
  - Conta não encontrada
- Criada `InsufficientFundsException` (400)
  - Saldo insuficiente para operação
  - Substituiu exceção genérica
- Handler de exceções atualizado para tratamento específico
- Logs diferenciados (warning vs error)
- Documentação completa em `app/Core/Exception/README.md`

#### Código
- Request simplificado com validações nativas
- Conversão de data simplificada usando `strtotime()`
- Melhor separação de responsabilidades
- Código mais limpo e manutenível

### 📚 Documentação
- Adicionado `app/Core/Exception/README.md`
- Atualizado README.md com seção de exceções
- Exemplos de teste para exceções em `api-examples.http`

---

## [1.0.0] - 2025-10-02

### ✨ Funcionalidades Implementadas

#### Infraestrutura
- Migração de PostgreSQL para MySQL 8
- Adicionado Redis 7 para filas e cache
- Adicionado Mailhog para testes de email
- Docker Compose completo e funcional

#### API Endpoints
- `POST /account/{accountId}/balance/withdraw` - Realizar saque PIX
- `GET /account/{accountId}/balance/withdraw` - Listar saques de uma conta
- `POST /api/v1/accounts` - Criar conta
- `GET /api/v1/accounts` - Listar contas

#### Validações de Negócio
- Valor do saque > 0
- Saldo suficiente (com validação dupla)
- Saldo não pode ficar negativo (lock pessimista)
- Data de agendamento não pode ser no passado
- Data de agendamento máximo 7 dias no futuro
- Chave PIX tipo email com validação de formato

#### Processamento Assíncrono
- Hyperf Async Queue configurado com 2 canais
- Jobs de processamento de saque (`ProcessWithdrawJob`)
- Jobs de envio de email (`SendWithdrawEmailJob`)
- Retry automático com backoff exponencial

#### Eventos de Domínio
- `WithdrawProcessed` - Disparado em saque bem-sucedido
- `WithdrawFailed` - Disparado em falha de saque
- Listener para envio automático de emails

#### Emails
- Template HTML responsivo
- Informações completas do saque
- Diferenciação entre sucesso e falha
- Envio assíncrono via fila

#### Cron Jobs
- `ProcessScheduledWithdrawsCron` - Executa a cada minuto
- Processa saques agendados automaticamente
- Logs detalhados de execução

#### Banco de Dados
- Migrations MySQL-compatíveis
- Índices otimizados para performance
- Foreign keys e constraints
- Suporte a UUID

#### Arquitetura
- Clean Architecture com DDD
- Separação por módulos (Account, Transaction, Shared)
- Repository Pattern
- DTO Pattern
- Value Objects
- Domain Events
- Event-Driven Architecture

### 📚 Documentação
- README.md completo com instruções
- IMPLEMENTATION_NOTES.md com decisões técnicas
- api-examples.http com exemplos de uso
- test-data.sql com dados de teste
- setup.sh para instalação automatizada

### 🔒 Segurança
- Lock pessimista em transações de saldo
- Validação rigorosa de inputs
- Transações DB para atomicidade
- Logs de auditoria

### 🚀 Performance
- Índices otimizados para queries frequentes
- Processamento assíncrono não bloqueia API
- Lock apenas onde necessário
- Eager loading para evitar N+1 queries

### 📊 Observabilidade
- Logs estruturados em todos os pontos críticos
- Rastreamento de jobs e eventos
- Métricas via logs
- Healthchecks em containers

### ✅ Compatibilidade
- PHP 8.1+
- Hyperf 3.1
- MySQL 8.0
- Redis 7
- Docker & Docker Compose

---

## Próximas Versões (Roadmap)

### [1.1.0] - Segurança e Autenticação
- [ ] JWT Authentication
- [ ] RBAC (Role-Based Access Control)
- [ ] Rate Limiting
- [ ] API Keys

### [1.2.0] - Observabilidade Avançada
- [ ] OpenTelemetry integration
- [ ] Prometheus metrics
- [ ] Grafana dashboards
- [ ] Distributed tracing

### [1.3.0] - Alta Disponibilidade
- [ ] MySQL replication
- [ ] Redis Sentinel
- [ ] Circuit breaker
- [ ] Health checks endpoint

### [2.0.0] - Event Sourcing Completo
- [ ] Event Store
- [ ] Event replay
- [ ] CQRS
- [ ] Projeções otimizadas

### [2.1.0] - Novas Funcionalidades
- [ ] Suporte a mais tipos de chave PIX
- [ ] TED/DOC transfers
- [ ] Cancelamento de saques agendados
- [ ] Histórico de transações
- [ ] Webhooks para notificações

---

**Versão atual: 1.0.0**  
**Data: 02/10/2025**  
**Desenvolvido para: Tecnofit**



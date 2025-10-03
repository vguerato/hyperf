# Tecnofit - Sistema de Saque PIX

## Requisitos

- Docker & Docker Compose
- Git

## Instalação e Execução

```bash
git clone <repository-url>
cd tecnofit
cp .env.example .env
docker-compose up -d
```

## Acessos

- API: http://localhost:8080
- Swagger: http://localhost:8081
- Mailhog: http://localhost:8025
- Grafana: http://localhost:3000 (admin/admin)
- Prometheus: http://localhost:9090
- Métricas: http://localhost:9502/metrics

## Testes

```bash
docker exec -it hyperf composer test
docker exec -it hyperf composer test:unit
docker exec -it hyperf composer test:feature
docker exec -it hyperf composer test:coverage
```
## Roadmap

- Idempotency
- Ledger

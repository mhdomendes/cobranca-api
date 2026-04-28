# Mini Sistema Financeiro

Projeto desenvolvido para o desafio de criar um mini sistema financeiro com as seguintes funcionalidades:

- Criar cobranca
- Registrar pagamento
- Calcular juros

O projeto foi estruturado em camadas, com foco em separacao de responsabilidades e dominio desacoplado de framework.

## Arquitetura

A aplicacao segue uma divisao em quatro camadas:

- `Domain`: contem as regras de negocio, entidades, value objects e contratos
- `Application`: contem os casos de uso da aplicacao
- `Infrastructure`: contem implementacoes de banco de dados, gateways e detalhes tecnicos
- `Interface`: contem a entrada e saida da aplicacao, neste caso a interface HTTP

O dominio nao depende de Laravel nem de componentes de framework, atendendo ao requisito do desafio.

## Estrutura de pastas

```text
src/
  Application/
    UseCase/
  Domain/
    Collect/
    Debt/
    Pay/
    Tax/
  Infrastructure/
    Database/
    Gateway/
    Repository/
  Interface/
    Http/
```

## Funcionalidades

### 1. Criar cobranca

Responsavel por receber os dados da cobranca, validar CPF, valor e data de vencimento, e persistir o registro.

Arquivos principais:

- [CreateCollectUseCase.php](/D:/Desenvolvimento/cobrancas-api/src/Application/UseCase/Collect/CreateCollectUseCase.php)
- [Collect.php](/D:/Desenvolvimento/cobrancas-api/src/Domain/Collect/Entity/Collect.php)
- [CollectRepositoryPostgres.php](/D:/Desenvolvimento/cobrancas-api/src/Infrastructure/Repository/Collect/CollectRepositoryPostgres.php)

### 2. Registrar pagamento

Responsavel por localizar a divida em aberto, calcular juros quando houver atraso, registrar o pagamento e atualizar o status da divida.

Arquivos principais:

- [RegisterPaymentUseCase.php](/D:/Desenvolvimento/cobrancas-api/src/Application/UseCase/Pay/RegisterPaymentUseCase.php)
- [Payment.php](/D:/Desenvolvimento/cobrancas-api/src/Domain/Pay/Entity/Payment.php)
- [Debt.php](/D:/Desenvolvimento/cobrancas-api/src/Domain/Debt/Entity/Debt.php)

### 3. Calcular juros

O calculo de juros foi isolado em um contrato de gateway no dominio e implementado na infraestrutura.

Arquivos principais:

- [TaxGatewayInterface.php](/D:/Desenvolvimento/cobrancas-api/src/Domain/Tax/Gateway/TaxGatewayInterface.php)
- [TaxPixGateway.php](/D:/Desenvolvimento/cobrancas-api/src/Infrastructure/Gateway/Tax/TaxPixGateway.php)
- [TaxBoletoGateway.php](/D:/Desenvolvimento/cobrancas-api/src/Infrastructure/Gateway/Tax/TaxBoletoGateway.php)

## Rotas HTTP

As rotas estao definidas em [routes.php](/D:/Desenvolvimento/cobrancas-api/src/Interface/Http/routes.php).

### Criar cobranca

```http
POST /collect
Content-Type: application/json
```

Exemplo de body:

```json
{
  "cpf": "12345678909",
  "amount": 150.00,
  "data_vencimento": "2026-05-10"
}
```

### Registrar pagamento

```http
POST /pay
Content-Type: application/json
```

Exemplo de body:

```json
{
  "cpf": "12345678909"
}
```

## Como executar

### 1. Instalar dependencias

```bash
composer install
```

### 2. Ajustar conexao com banco

Editar o arquivo [Connection.php](/D:/Desenvolvimento/cobrancas-api/src/Infrastructure/Database/Connection.php) com as credenciais locais do PostgreSQL.

### 3. Subir a aplicacao

```bash
php -S localhost:8000 index.php
```

## Ajustes necessarios para fechar o fluxo

Durante a revisao, foram identificados quatro pontos importantes para deixar o projeto consistente de ponta a ponta:

### 1. Permitir pagamento sem atraso

No caso de uso de pagamento, os juros devem ser aplicados somente quando o valor calculado for maior que o valor atual da divida.

### 2. Unificar cobranca e divida no fluxo principal

Hoje a cobranca e criada em `collects`, enquanto o pagamento consulta `debts`. Para o fluxo funcionar corretamente, o ideal e usar uma unica fonte de verdade para a cobranca que sera paga.

Uma forma simples de fechar o desafio:

- `criar cobranca` cria uma `Debt`
- `registrar pagamento` busca essa `Debt`
- `calcular juros` opera sobre essa mesma `Debt`

### 3. Preservar status ao carregar cobranca

A entidade `Collect` precisa aceitar o `status` no construtor para nao perder o valor persistido no banco ao ser reidratada.

### 4. Retornar o ID gerado na criacao

O repositório de cobranca precisa atualizar a entidade com o `lastInsertId()` apos o `insert`, para que a resposta da API retorne o identificador correto.

## Pontos positivos da solucao

- Dominio separado da infraestrutura
- Casos de uso organizados na camada de aplicacao
- Contratos definidos no dominio
- Interface HTTP simples e direta
- Projeto leve, sem dependencia de framework no nucleo de negocio

## Melhorias futuras

- Adicionar testes unitarios para entidades e casos de uso
- Criar tratamento padrao de excecoes na interface HTTP
- Separar DTOs de entrada e saida dos casos de uso
- Criar um caso de uso explicito para calcular juros
- Configurar injecao de dependencia de forma centralizada

## Resumo

O projeto atende bem a proposta arquitetural do desafio, especialmente no isolamento do dominio e na separacao por camadas. Com os ajustes identificados na revisao, o fluxo de cobranca, calculo de juros e pagamento fica coerente e pronto para apresentacao tecnica.

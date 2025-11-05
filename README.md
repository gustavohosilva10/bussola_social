# 🛒 Carrinho de Compras

Aplicação de carrinho de compras com arquitetura de microsserviços usando Laravel Octane (API) e React (Frontend).

## 🚀 Como Executar

### Pré-requisitos
- Docker
- Docker Compose

### Instalação e Execução

```bash
# Clone o repositório
git clone https://github.com/gustavohosilva10/bussola_social.git
cd bussola_social

# Inicie os containers
docker-compose up -d --build
```

### Acesse a aplicação
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8003

## 🧪 Testes

```bash
docker-compose exec backend php artisan test
```

**Resultado**: 23 testes, 108 assertions ✅

## 🏗️ Arquitetura

### Backend (Laravel Octane + Swoole)
- Padrão: `Request → Controller → Interface → Repository`
- DTOs para transferência de dados
- Totalmente tipado (PHP 8.2)
- Injeção de dependências

### Frontend (React + Vite)
- Interface em português
- Hot-reloading
- Comunicação com API REST

### Infraestrutura
- Docker Compose para orquestração
- Volumes para desenvolvimento com hot-reload

## 📦 Funcionalidades

### Produtos
- 5 produtos pré-cadastrados
- Listagem via API REST

### Carrinho de Compras
- Adicionar/remover produtos
- Ajustar quantidades
- Calcular total com diferentes formas de pagamento

### Formas de Pagamento

1. **PIX**: 10% de desconto
2. **Cartão de Crédito à Vista**: 10% de desconto
3. **Cartão de Crédito Parcelado**: 
   - 2x a 12x
   - 1% de juros compostos por parcela
   - Fórmula: M = P × (1 + 0,01)^n

## 📡 API

### Listar Produtos
```http
GET http://localhost:8003/api/products
```

### Calcular Carrinho
```http
POST http://localhost:8003/api/cart/calculate
Content-Type: application/json

{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "payment_method": "PIX",
  "installments": 1
}
```

### Métodos de Pagamento Disponíveis
- `PIX`
- `CREDIT_CARD_FULL_PAYMENT`
- `CREDIT_CARD_INSTALLMENTS` (installments: 2-12)

## 🛠️ Comandos Úteis

### Backend
```bash
# Acessar container
docker-compose exec backend bash

# Rodar testes
php artisan test

# Ver logs
docker logs shopping-cart-backend
```

### Frontend
```bash
# Acessar container
docker-compose exec frontend sh

# Ver logs
docker logs shopping-cart-frontend
```

### Parar os containers
```bash
docker-compose down
```

## 📁 Estrutura do Projeto

```
bussola_social/
├── docker-compose.yml
├── backend/                    # Laravel Octane API
│   ├── app/
│   │   ├── DTOs/              # Data Transfer Objects
│   │   ├── Http/Controllers/  # Controladores
│   │   ├── Interfaces/        # Contratos
│   │   ├── Repositories/      # Repositórios
│   │   └── Services/          # Serviços
│   └── tests/                 # Testes PHPUnit
└── frontend/                   # React App
    └── src/
        ├── App.jsx            # Componente principal
        └── App.css            # Estilos
```

## 💡 Tecnologias

- **Backend**: Laravel 12, Octane, Swoole, PHP 8.2
- **Frontend**: React 18, Vite
- **Containerização**: Docker, Docker Compose
- **Testes**: PHPUnit

## 📝 Notas

Este projeto foi desenvolvido como parte de um processo seletivo.

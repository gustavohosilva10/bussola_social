# 🛒 Shopping Cart Application

A modern microservices architecture application with Laravel Octane (API) and React (Frontend).

[![Tests](https://img.shields.io/badge/tests-23%20passed-brightgreen)]()
[![Assertions](https://img.shields.io/badge/assertions-108-blue)]()
[![Architecture](https://img.shields.io/badge/architecture-Request→Controller→Interface→Repository-orange)]()

## 🏗️ Architecture

- **Backend**: Laravel 12 + Octane (Swoole) - High-performance API
- **Frontend**: React 18 + Vite - Modern UI with hot-reloading
- **Infrastructure**: Docker Compose for orchestration
- **Tests**: PHPUnit with 23 tests and 108 assertions

### Architecture Pattern

```
Request → Controller → Interface → Repository/Service
```

All code follows this strict architectural pattern with:
- ✅ Full PHP 8.2 type hints
- ✅ DTOs for data transfer
- ✅ Interface contracts
- ✅ Dependency injection
- ✅ Code in English

## 🚀 Quick Start

### Prerequisites
- Docker
- Docker Compose

### Installation

1. Navigate to the project directory:
```bash
cd /home/gholiveira/www/processos_seletivos/bussola_social
```

2. Start the services:
```bash
docker-compose up -d --build
```

3. Access the application:
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8003/api/products

### Run Tests

```bash
docker-compose exec backend php artisan test
```

**Result**: 23 tests, 108 assertions, all passing ✅

### API Endpoints

#### Get Products
```
GET http://localhost:8003/api/products
```

#### Calculate Cart Total
```
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

### Payment Methods

- **PIX**: 10% discount
- **CREDIT_CARD_FULL_PAYMENT**: 10% discount
- **CREDIT_CARD_INSTALLMENTS**: 1% compound interest per installment (2x to 12x)

### Compound Interest Formula

For installments: M = P × (1 + i)^n

Where:
- M = Final amount
- P = Principal (cart subtotal)
- i = Interest rate (0.01 or 1%)
- n = Number of installments

## 🧪 Running Tests

```bash
docker-compose exec backend php artisan test
```

## 📁 Project Structure

```
shopping-cart-app/
├── docker-compose.yml
├── backend/                 # Laravel Octane API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   ├── Repositories/
│   │   ├── Interfaces/
│   │   └── DTOs/
│   └── tests/
└── frontend/               # React Application
    └── src/
```

## 🛠️ Development

The project uses Docker volumes for hot-reloading in both backend and frontend.

### Backend Commands

```bash
# Access backend container
docker-compose exec backend bash

# Run migrations
php artisan migrate

# Run tests
php artisan test
```

### Frontend Commands

```bash
# Access frontend container
docker-compose exec frontend sh

# Install new package
npm install package-name
```

## 📝 License

This project is for evaluation purposes.

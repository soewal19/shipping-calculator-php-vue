# Калькулятор стоимости доставки (Shipping Cost Calculator)

## О проекте
Данный проект представляет собой веб-приложение для расчета стоимости доставки различными перевозчиками (TransCompany, PackGroup). 
Реализован с использованием Symfony (Backend) и Vue.js (Frontend), упакован в Docker контейнеры.

## Структура проекта

```
shipping-calculator/
├── backend/                # Symfony API (Core logic)
│   ├── config/             # Configuration files
│   ├── src/                # Source code
│   │   ├── Application/    # Application Layer (Services)
│   │   ├── Domain/         # Domain Layer (Models, Interfaces)
│   │   └── Infrastructure/ # Infrastructure Layer (Controllers, WebSocket, Strategies)
│   └── ...
├── frontend/               # Vue.js UI
│   ├── src/                # Vue components and logic
│   └── ...
├── docker/                 # Docker config (php, nginx, ui)
├── docs/                   # Documentation (C4 models, API spec)
├── CI/CD/                  # Deployment & Test scripts
├── E2E/                    # Playwright End-to-End tests
├── logs/                   # Application logs
├── docker-compose.yml      # Orchestration
└── README.md
```


## Требования
- Docker
- Docker Compose

## Установка и запуск

1. **Клонирование репозитория**
   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```

2. **Запуск контейнеров**
   ```bash
   docker-compose up -d --build
   ```

3. **Доступ к приложению**
   - Frontend: [http://localhost:8080](http://localhost:8080)
   - API: [http://localhost:8080/api](http://localhost:8080/api) (через Nginx прокси)

4. **Запуск тестов**
   
   Проект содержит скрипты для запуска тестов. Основной скрипт запускает Unit тесты бэкенда и E2E тесты фронтенда.

   **Автоматический запуск всех тестов:**
   ```bash
   ./CI/CD/test.sh
   ```

   **Запуск E2E тестов (Playwright) вручную:**
   Для детальной отладки UI тестов можно использовать следующие команды:
   ```bash
   cd E2E
   npm install        # Установка зависимостей (первый запуск)
   npx playwright test # Запуск тестов в headless режиме
   npx playwright test --ui # Запуск в интерактивном режиме с UI
   ```

## Архитектура
Проект построен по принципам чистой архитектуры и SOLID.
Используется паттерн **Strategy** для реализации логики расчета стоимости различных перевозчиков, что позволяет легко добавлять новых без изменения существующего кода.

### Реализация SOLID и Интерфейсов
Принципы SOLID полностью соблюдены для обеспечения гибкости и расширяемости системы:

- **Single Responsibility Principle (SRP)**:
  - `ShippingController`: Отвечает только за обработку HTTP запросов.
  - `ShippingService`: Управляет выбором стратегии.
  - Стратегии (`TransCompanyStrategy`, `PackGroupStrategy`): Содержат только логику расчета цены.

- **Open/Closed Principle (OCP)**:
  - Система открыта для расширения: новый перевозчик добавляется созданием нового класса стратегии.
  - Система закрыта для модификации: код сервиса не меняется при добавлении новых перевозчиков.

- **Liskov Substitution Principle (LSP)**:
  - Все стратегии реализуют интерфейс `ShippingStrategyInterface` и взаимозаменяемы.

- **Interface Segregation Principle (ISP)**:
  - Интерфейс `ShippingStrategyInterface` содержит только необходимые методы (`calculate`, `supports`).

- **Dependency Inversion Principle (DIP)**:
  - `ShippingService` зависит от абстракции (`ShippingStrategyInterface`), а не от конкретных реализаций.

Подробная документация архитектуры доступна в [docs/c4-model.md](docs/c4-model.md).
Описание API доступно в [docs/api.yaml](docs/api.yaml).

---

# Shipping Cost Calculator (English Version)

## About
This project is a web application for calculating shipping costs for various carriers (TransCompany, PackGroup). 
Implemented using Symfony (Backend) and Vue.js (Frontend), packaged in Docker containers.

## Project Structure

```
shipping-calculator/
├── backend/                # Symfony API (Core logic)
│   ├── config/             # Configuration files
│   ├── src/                # Source code
│   │   ├── Application/    # Application Layer (Services)
│   │   ├── Domain/         # Domain Layer (Models, Interfaces)
│   │   └── Infrastructure/ # Infrastructure Layer (Controllers, WebSocket, Strategies)
│   └── ...
├── frontend/               # Vue.js UI
│   ├── src/                # Vue components and logic
│   └── ...
├── docker/                 # Docker config (php, nginx, ui)
├── docs/                   # Documentation (C4 models, API spec)
├── CI/CD/                  # Deployment & Test scripts
├── E2E/                    # Playwright End-to-End tests
├── logs/                   # Application logs
├── docker-compose.yml      # Orchestration
└── README.md
```


## Requirements
- Docker
- Docker Compose

## Installation and execution

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```

2. **Run containers**
   ```bash
   docker-compose up -d --build
   ```

3. **Access the application**
   - Frontend: [http://localhost:8080](http://localhost:8080)
   - API: [http://localhost:8080/api](http://localhost:8080/api) (via Nginx proxy)

4. **Run tests**
   
   The project contains scripts to run tests. The main script runs unit tests for the backend and E2E tests for the frontend.

   **Automatic run of all tests:**
   ```bash
   ./CI/CD/test.sh
   ```

   **Manual run of E2E tests (Playwright):**
   For detailed UI test debugging, use the following commands:
   ```bash
   cd E2E
   npm install        # Install dependencies (first run)
   npx playwright test # Run tests in headless mode
   npx playwright test --ui # Run in interactive mode with UI
   ```

## Architecture
The project is built on Clean Architecture and SOLID principles.
The **Strategy** pattern is used to implement shipping cost calculation logic for different carriers, allowing easy addition of new ones without changing existing code.

### SOLID Implementation and Interfaces
SOLID principles are fully observed to ensure system flexibility and extensibility:

- **Single Responsibility Principle (SRP)**:
  - `ShippingController`: Responsible only for handling HTTP requests.
  - `ShippingService`: Manages strategy selection.
  - Strategies (`TransCompanyStrategy`, `PackGroupStrategy`): Contain only price calculation logic.

- **Open/Closed Principle (OCP)**:
  - System is open for extension: a new carrier is added by creating a new strategy class.
  - System is closed for modification: service code does not change when adding new carriers.

- **Liskov Substitution Principle (LSP)**:
  - All strategies implement `ShippingStrategyInterface` and are interchangeable.

- **Interface Segregation Principle (ISP)**:
  - `ShippingStrategyInterface` contains only necessary methods (`calculate`, `supports`).

- **Dependency Inversion Principle (DIP)**:
  - `ShippingService` depends on abstraction (`ShippingStrategyInterface`), not concrete implementations.

Detailed architecture documentation is available in [docs/c4-model.md](docs/c4-model.md).

---

# Status of Implementation / Статус Реализации

## ✅ Implemented Features / Реализовано

### 1. Backend (Symfony 6.4 + PHP 8.2)
- **Clean Architecture**: Domain, Application, Infrastructure layers.
- **Strategy Pattern**: `TransCompany` (>10kg: 100€, <=10kg: 20€), `PackGroup` (1€/kg).
- **API**: REST Controller (`POST /api/shipping/calculate`).
- **WebSocket**: Real-time server (Ratchet) on port 8081.
- **Security**: Custom CORS Listener (`CorsAccessControlListener`) with **Blacklist/Whitelist** support.
- **Logging**: Monolog with `RotatingFileHandler` (logs to `logs/app.log`).
- **Docs**: Swagger/OpenAPI integration.

### 2. Frontend (Vue.js 3 + Tailwind CSS)
- **UI**: Premium design shipping calculator.
- **Transports**: Support for both **HTTP** (REST) and **WebSocket** communication.
- **UX**: Real-time status updates and error handling.

### 3. DevOps & Infrastructure
- **Docker**: Nginx, PHP-FPM, Node.js, WebSocket containers.
- **CI/CD**: Deployment and test scripts (`deploy.sh`, `test.sh`).
- **Deployment Plan**: Detailed guide for `ho.ua` (VPS/Shared) in `DEPLOY_PLAN.md`.

### 4. QA & Testing
- **E2E**: Playwright tests covering calculation scenarios and validation.

## ⏳ Pending / Ожидается
- **Environment**: Requires `docker-compose up` execution on the host machine.
- **Tests**: Run `./CI/CD/test.sh` after containers are up.


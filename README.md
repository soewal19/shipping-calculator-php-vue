# Shipping Cost Calculator / Калькулятор стоимости доставки

> Документация ниже двуязычная: сначала **русский**, затем **English**.

---

## RU

### О проекте
Веб-приложение для расчёта стоимости доставки по двум перевозчикам:
- `transcompany` — фиксированная стоимость по порогу веса;
- `packgroup` — линейный расчёт (1 EUR за кг).

Стек: **Symfony (backend)** + **Vue 3 (frontend)** + **Docker** + **Playwright E2E**.

### Быстрый запуск
1. Соберите фронтенд:
   ```bash
   cd frontend
   npm install
   npm run build
   ```
2. Поднимите контейнеры:
   ```bash
   docker-compose up -d --build
   ```

Доступ:
- Frontend: `http://localhost:8080`
- API: `http://localhost:8080/api`
- Swagger UI: `http://localhost:8080/api/doc`
- OpenAPI JSON: `http://localhost:8080/api/doc.json`
- WebSocket: `ws://localhost:8081`

### Полное дерево файлов проекта (с описанием)

> Ниже приведено полное дерево **проектных и конфигурационных файлов**. Большие автогенерируемые артефакты (например, `E2E/node_modules`, `E2E/playwright-report`, `E2E/test-results`) отмечены как служебные каталоги и не разворачиваются построчно.

```text
.
├── .gitignore                                 # Игнорируемые Git файлы
├── Coding Test — Junior PHP Developer (Symfony) (1).pdf  # Исходное тестовое задание
├── docker-compose.yml                         # Оркестрация контейнеров
├── EXECUTION_REPORT.md                        # Отчёт по выполнению работ
├── LICENSE                                    # Лицензия проекта
├── README.md                                  # Основная документация (этот файл)
├── deploy_to_github.ps1                       # PowerShell-скрипт публикации в GitHub
├── force_push.ps1                             # PowerShell-скрипт принудительного пуша
│
├── backend/                                   # Backend (Symfony)
│   ├── .env                                   # Переменные окружения (base)
│   ├── .env.dev                               # Переменные окружения dev
│   ├── .gitignore                             # Игнор файлов backend
│   ├── composer.json                          # PHP-зависимости и автозагрузка
│   ├── composer.lock                          # Зафиксированные версии зависимостей
│   ├── symfony.lock                           # Lock Symfony recipes
│   ├── bin/
│   │   └── console                            # Symfony CLI entrypoint
│   ├── config/
│   │   ├── bundles.php                        # Регистрация Symfony bundles
│   │   ├── preload.php                        # Preload-конфигурация
│   │   ├── routes.yaml                        # Маршруты приложения
│   │   ├── services.yaml                      # DI-контейнер и сервисы
│   │   ├── packages/
│   │   │   ├── cache.yaml                     # Кэш
│   │   │   ├── framework.yaml                 # Framework settings
│   │   │   ├── monolog.yaml                   # Логирование
│   │   │   ├── nelmio_api_doc.yaml            # Swagger/OpenAPI
│   │   │   ├── nelmio_cors.yaml               # CORS
│   │   │   ├── routing.yaml                   # Routing package settings
│   │   │   └── validator.yaml                 # Валидация
│   │   └── routes/
│   │       ├── framework.yaml                 # Framework routes
│   │       └── nelmio_api_doc.yaml            # Routes для API docs
│   ├── logs/                                  # Логи backend
│   ├── public/
│   │   └── index.php                          # Front controller
│   ├── src/
│   │   ├── Kernel.php                         # Ядро Symfony
│   │   ├── Application/
│   │   │   └── ShippingService.php            # Сервис расчёта и выбор стратегии
│   │   ├── Controller/
│   │   │   └── .gitignore                     # Заглушка каталога
│   │   ├── Domain/
│   │   │   ├── ShippingStrategyInterface.php  # Контракт стратегии расчёта
│   │   │   └── Model/
│   │   │       └── ShippingRequest.php        # DTO/модель входных данных + validation
│   │   └── Infrastructure/
│   │       ├── Controller/
│   │       │   ├── ApiDocController.php       # Контроллер/точка API-документации
│   │       │   └── ShippingController.php     # REST endpoint расчёта доставки
│   │       ├── EventListener/
│   │       │   └── CorsAccessControlListener.php # CORS ACL (blacklist/whitelist)
│   │       ├── Strategy/
│   │       │   ├── PackGroupStrategy.php      # Стратегия PackGroup
│   │       │   └── TransCompanyStrategy.php   # Стратегия TransCompany
│   │       └── WebSocket/
│   │           ├── RunWebSocketServerCommand.php # Symfony command запуска WS сервера
│   │           └── ShippingMessageHandler.php # WS-обработчик расчётов
│   └── var/                                   # Кэш/временные данные Symfony
│
├── CI/
│   └── CD/
│       ├── deploy.sh                          # Скрипт деплоя
│       └── test.sh                            # Скрипт прогонки тестов
│
├── docker/
│   ├── nginx/
│   │   ├── default.conf                       # Nginx-конфигурация (reverse proxy + static)
│   │   └── Dockerfile                         # Образ nginx
│   ├── php/
│   │   └── Dockerfile                         # Образ php-fpm/symfony
│   └── ui/
│       └── Dockerfile                         # Образ frontend/ui
│
├── docs/
│   ├── api.yaml                               # OpenAPI спецификация
│   ├── c4-model.md                            # C4 Level 1-3 + обзор Level 4
│   ├── c4-level4-code.md                      # Подробная реализация C4 Level 4 (Code)
│   └── file-graph.md                          # Mermaid-граф структуры проекта
│
├── E2E/
│   ├── package.json                           # Зависимости E2E
│   ├── package-lock.json                      # Lock-файл E2E
│   ├── playwright.config.ts                   # Конфигурация Playwright
│   ├── README.md                              # Документация по E2E
│   ├── tests/
│   │   └── shipping.spec.ts                   # E2E тесты калькулятора
│   ├── playwright-report/                     # Автогенерируемые HTML-отчёты Playwright
│   └── test-results/                          # Автогенерируемые артефакты тестов
│
├── frontend/
│   ├── index.html                             # HTML entrypoint
│   ├── package.json                           # Зависимости frontend
│   ├── package-lock.json                      # Lock-файл frontend
│   ├── postcss.config.js                      # PostCSS-конфиг
│   ├── tailwind.config.js                     # Tailwind-конфиг
│   ├── vite.config.js                         # Vite-конфиг
│   └── src/
│       ├── App.vue                            # Корневой Vue-компонент
│       ├── main.js                            # Инициализация Vue-приложения
│       ├── style.css                          # Глобальные стили
│       └── components/
│           └── ShippingCalculator.vue         # Основной UI-калькулятор (HTTP + WS)
│
├── logs/                                      # Общие/внешние логи проекта
├── DeployFinal/                               # Временная/служебная папка деплоя (сейчас пусто)
└── DeployTemp/                                # Временная/служебная папка деплоя (сейчас пусто)
```

### C4-документация
- Уровни 1–3: `docs/c4-model.md`
- Реализация 4-го уровня (Code): `docs/c4-level4-code.md`

### Тесты
```bash
./CI/CD/test.sh
```

---

## EN

### About
This is a shipping cost calculator web app for two carriers:
- `transcompany` — threshold-based fixed pricing;
- `packgroup` — linear pricing (1 EUR per kg).

Stack: **Symfony (backend)** + **Vue 3 (frontend)** + **Docker** + **Playwright E2E**.

### Quick start
1. Build frontend assets:
   ```bash
   cd frontend
   npm install
   npm run build
   ```
2. Start containers:
   ```bash
   docker-compose up -d --build
   ```

Access points:
- Frontend: `http://localhost:8080`
- API: `http://localhost:8080/api`
- Swagger UI: `http://localhost:8080/api/doc`
- OpenAPI JSON: `http://localhost:8080/api/doc.json`
- WebSocket: `ws://localhost:8081`

### Full project file tree (with file descriptions)

```text
.
├── .gitignore                                 # Git ignored files list
├── Coding Test — Junior PHP Developer (Symfony) (1).pdf  # Original coding task
├── docker-compose.yml                         # Container orchestration
├── EXECUTION_REPORT.md                        # Execution/report notes
├── LICENSE                                    # Project license
├── README.md                                  # Main documentation
├── deploy_to_github.ps1                       # PowerShell script for GitHub deploy
├── force_push.ps1                             # PowerShell force-push helper
│
├── backend/                                   # Symfony backend
│   ├── .env                                   # Base environment variables
│   ├── .env.dev                               # Dev environment variables
│   ├── .gitignore                             # Backend gitignore
│   ├── composer.json                          # PHP dependencies/autoload
│   ├── composer.lock                          # Locked PHP dependency versions
│   ├── symfony.lock                           # Symfony recipes lock
│   ├── bin/
│   │   └── console                            # Symfony CLI entrypoint
│   ├── config/
│   │   ├── bundles.php                        # Registered Symfony bundles
│   │   ├── preload.php                        # Preload config
│   │   ├── routes.yaml                        # App routes
│   │   ├── services.yaml                      # DI service wiring
│   │   ├── packages/
│   │   │   ├── cache.yaml                     # Cache settings
│   │   │   ├── framework.yaml                 # Framework settings
│   │   │   ├── monolog.yaml                   # Logging settings
│   │   │   ├── nelmio_api_doc.yaml            # Swagger/OpenAPI config
│   │   │   ├── nelmio_cors.yaml               # CORS config
│   │   │   ├── routing.yaml                   # Routing package settings
│   │   │   └── validator.yaml                 # Validation rules config
│   │   └── routes/
│   │       ├── framework.yaml                 # Framework default routes
│   │       └── nelmio_api_doc.yaml            # Routes for API docs
│   ├── logs/                                  # Backend logs
│   ├── public/
│   │   └── index.php                          # Front controller
│   ├── src/
│   │   ├── Kernel.php                         # Symfony kernel
│   │   ├── Application/
│   │   │   └── ShippingService.php            # Core shipping calculation service
│   │   ├── Controller/
│   │   │   └── .gitignore                     # Placeholder for empty directory
│   │   ├── Domain/
│   │   │   ├── ShippingStrategyInterface.php  # Strategy contract
│   │   │   └── Model/
│   │   │       └── ShippingRequest.php        # Request model + validation constraints
│   │   └── Infrastructure/
│   │       ├── Controller/
│   │       │   ├── ApiDocController.php       # Swagger UI endpoint
│   │       │   └── ShippingController.php     # REST shipping endpoint
│   │       ├── EventListener/
│   │       │   └── CorsAccessControlListener.php # CORS whitelist/blacklist guard
│   │       ├── Strategy/
│   │       │   ├── PackGroupStrategy.php      # PackGroup shipping strategy
│   │       │   └── TransCompanyStrategy.php   # TransCompany shipping strategy
│   │       └── WebSocket/
│   │           ├── RunWebSocketServerCommand.php # CLI command to start WS server
│   │           └── ShippingMessageHandler.php # WS message handler
│   └── var/                                   # Symfony cache/temp files
│
├── CI/
│   └── CD/
│       ├── deploy.sh                          # Deployment script
│       └── test.sh                            # Test run script
│
├── docker/
│   ├── nginx/
│   │   ├── default.conf                       # Nginx reverse proxy/static config
│   │   └── Dockerfile                         # Nginx image definition
│   ├── php/
│   │   └── Dockerfile                         # PHP-FPM/Symfony image definition
│   └── ui/
│       └── Dockerfile                         # Frontend build/runtime image
│
├── docs/
│   ├── api.yaml                               # OpenAPI specification
│   ├── c4-model.md                            # C4 model (levels 1-3 + level 4 overview)
│   ├── c4-level4-code.md                      # Detailed C4 Level 4 (Code) implementation
│   └── file-graph.md                          # Mermaid file-structure graph
│
├── E2E/
│   ├── package.json                           # E2E dependencies
│   ├── package-lock.json                      # E2E lock file
│   ├── playwright.config.ts                   # Playwright configuration
│   ├── README.md                              # E2E documentation
│   ├── tests/
│   │   └── shipping.spec.ts                   # Shipping calculator E2E tests
│   ├── playwright-report/                     # Generated Playwright HTML reports
│   └── test-results/                          # Generated test artifacts
│
├── frontend/
│   ├── index.html                             # App HTML entrypoint
│   ├── package.json                           # Frontend dependencies
│   ├── package-lock.json                      # Frontend lock file
│   ├── postcss.config.js                      # PostCSS configuration
│   ├── tailwind.config.js                     # Tailwind configuration
│   ├── vite.config.js                         # Vite configuration
│   └── src/
│       ├── App.vue                            # Root Vue component
│       ├── main.js                            # App bootstrap
│       ├── style.css                          # Global styles
│       └── components/
│           └── ShippingCalculator.vue         # Main calculator component (HTTP + WS)
│
├── logs/                                      # Global/host logs directory
├── DeployFinal/                               # Temporary deployment directory (currently empty)
└── DeployTemp/                                # Temporary deployment directory (currently empty)
```

### C4 documentation
- Levels 1–3: `docs/c4-model.md`
- Level 4 implementation (Code): `docs/c4-level4-code.md`

### Tests
```bash
./CI/CD/test.sh
```


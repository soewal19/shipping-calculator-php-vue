# C4 Level 4 (Code) — Shipping Cost Calculator

> Этот документ расширяет C4-модель проекта до **уровня 4 (Code)**.
> Уровни 1–3 см. в `docs/c4-model.md`.

---

## RU — Уровень 4 (Code)

### 1) Backend: ключевые классы и зависимости

```mermaid
classDiagram
    direction LR

    class ShippingController {
        -ShippingService shippingService
        -ValidatorInterface validator
        +calculateInfo() JsonResponse
        +calculate(Request request) JsonResponse
    }

    class ShippingService {
        -iterable~ShippingStrategyInterface~ strategies
        -LoggerInterface logger
        +calculate(ShippingRequest request) float
    }

    class ShippingRequest {
        +string carrier
        +float weightKg
        +__construct(string carrier, float weightKg)
    }

    class ShippingStrategyInterface {
        <<interface>>
        +calculate(float weightKg) float
        +supports(string carrier) bool
    }

    class TransCompanyStrategy {
        +calculate(float weightKg) float
        +supports(string carrier) bool
    }

    class PackGroupStrategy {
        +calculate(float weightKg) float
        +supports(string carrier) bool
    }

    class ShippingMessageHandler {
        -ShippingService shippingService
        +onMessage(ConnectionInterface from, string msg)
    }

    class RunWebSocketServerCommand {
        -ShippingService shippingService
        +execute(InputInterface input, OutputInterface output) int
    }

    class CorsAccessControlListener {
        -array blacklistedOrigins
        -array whitelistedOrigins
        +onKernelRequest(RequestEvent event) void
    }

    ShippingController --> ShippingService : uses
    ShippingController ..> ShippingRequest : creates/validates
    ShippingService --> ShippingStrategyInterface : iterates
    TransCompanyStrategy ..|> ShippingStrategyInterface : implements
    PackGroupStrategy ..|> ShippingStrategyInterface : implements
    ShippingMessageHandler --> ShippingService : uses
    RunWebSocketServerCommand --> ShippingMessageHandler : creates
    RunWebSocketServerCommand --> ShippingService : injects
```

### 2) REST-поток (POST /api/shipping/calculate)

```mermaid
sequenceDiagram
    actor U as User (Browser/UI)
    participant C as ShippingController
    participant V as Symfony Validator
    participant S as ShippingService
    participant T as TransCompanyStrategy
    participant P as PackGroupStrategy

    U->>C: POST /api/shipping/calculate {carrier, weightKg}
    C->>C: decode JSON
    C->>V: validate(ShippingRequest)
    alt validation error
        C-->>U: 400 Validation failed
    else valid
        C->>S: calculate(request)
        S->>T: supports(carrier)?
        alt transcompany
            T-->>S: true
            S->>T: calculate(weightKg)
            T-->>S: price
        else packgroup
            T-->>S: false
            S->>P: supports(carrier)?
            P-->>S: true
            S->>P: calculate(weightKg)
            P-->>S: price
        else unsupported
            S-->>C: InvalidArgumentException
            C-->>U: 400 error
        end
        S-->>C: price
        C-->>U: 200 {carrier, weightKg, currency, price}
    end
```

### 3) WebSocket-поток

```mermaid
sequenceDiagram
    actor U as User (Browser/UI)
    participant W as WebSocket Server
    participant H as ShippingMessageHandler
    participant S as ShippingService
    participant STR as Strategy (TransCompany/PackGroup)

    U->>W: ws://localhost:8081 (connect)
    U->>W: JSON message {carrier, weightKg}
    W->>H: onMessage(msg)
    H->>S: calculate(request)
    S->>STR: supports()+calculate()
    STR-->>S: price
    S-->>H: price
    H-->>U: JSON {carrier, weightKg, currency, price}
```

### 4) Соответствие слоям Clean Architecture

- **Domain**
  - `ShippingStrategyInterface`
  - `Model/ShippingRequest`

- **Application**
  - `ShippingService` (оркестрация стратегий + логирование)

- **Infrastructure**
  - REST: `ShippingController`, `ApiDocController`
  - WS: `RunWebSocketServerCommand`, `ShippingMessageHandler`
  - Integrations: `CorsAccessControlListener`, concrete strategies

### 5) Расширение новым перевозчиком (на уровне кода)

1. Добавить класс стратегии, реализующий `ShippingStrategyInterface`.
2. Пометить стратегию тегом `app.shipping_strategy` в `backend/config/services.yaml`.
3. (Опционально) добавить тест-кейсы в `E2E/tests/shipping.spec.ts`.

---

## EN — Level 4 (Code)

### 1) Backend classes and dependencies

At code level, the core flow is:

- `ShippingController` receives HTTP requests and validates input.
- `ShippingService` selects a strategy from tagged services.
- Concrete strategies (`TransCompanyStrategy`, `PackGroupStrategy`) perform price calculation.
- `ShippingMessageHandler` reuses the same `ShippingService` for WebSocket requests.

### 2) REST flow

`POST /api/shipping/calculate` → Controller → Validation → Service → Strategy selection → JSON response.

### 3) WebSocket flow

WebSocket message (`carrier`, `weightKg`) → `ShippingMessageHandler` → `ShippingService` → strategy → JSON response.

### 4) Clean Architecture mapping

- **Domain**: interface + request model
- **Application**: orchestration service
- **Infrastructure**: controllers, websocket runtime, listeners, concrete adapters

### 5) How to add a new carrier

Implement `ShippingStrategyInterface`, register it with tag `app.shipping_strategy`, and add tests.

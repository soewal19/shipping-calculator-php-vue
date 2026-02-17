# E2E Tests

Этот директория содержит End-to-End тесты для приложения Shipping Cost Calculator, реализованные с помощью Playwright.

## Предварительные требования

- Node.js 18+
- Запущенное приложение (через Docker)

## Установка

```bash
cd E2E
npm install
npx playwright install --with-deps
```

## Запуск тестов

### Запуск всех тестов в headless режиме
```bash
npx playwright test
```

### Запуск с UI (для отладки)
```bash
npx playwright test --ui
```

### Просмотр отчета
```bash
npx playwright show-report
```

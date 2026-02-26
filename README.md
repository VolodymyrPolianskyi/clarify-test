# ZOO Shipping Calculator

Калькулятор стоимости доставки. Symfony 7 + Vue.js 3, всё в Docker.



---

## Запуск

```bash
cd zoo-shipping
docker-compose up --build
```

Первый запуск займёт 3-5 минут. После этого открыть в браузере:

| Сервис | URL |
|--------|-----|
| Frontend (Vue.js) | http://localhost:5173 |
| Backend API | http://localhost:8080 |

```bash
# Остановить
docker-compose down
```

---

## API

### GET /api/carriers
Список доступных перевозчиков.

```json
{
  "carriers": [
    { "slug": "transcompany", "name": "TransCompany" },
    { "slug": "packgroup",    "name": "PackGroup" }
  ]
}
```

### POST /api/shipping/calculate

```json
// Request
{ "carrier": "transcompany", "weightKg": 12.5 }

// Response 200
{ "carrier": "transcompany", "weightKg": 12.5, "currency": "EUR", "price": 100 }

// Response 422 — невалидные данные
{ "error": "Validation failed", "details": { "weightKg": "This value should be positive." } }

// Response 422 — неизвестный перевозчик
{ "error": "Unsupported carrier" }
```

---

## Тесты

Запускать при ранящемся докере
```bash
docker-compose exec php ./vendor/bin/phpunit
```

---

## Добавление нового перевозчика

Создать один файл в `backend/src/Strategy/` — больше ничего менять не нужно:

```php
class NewCarrierStrategy implements ShippingStrategyInterface
{
    public function getSlug(): string    { return 'newcarrier'; }
    public function getName(): string    { return 'New Carrier'; }
    public function calculatePrice(float $weightKg): float { return $weightKg * 2.5; }
}
```

Добавить тег в `backend/config/services.yaml`:

```yaml
App\Strategy\NewCarrierStrategy:
    tags: ['app.shipping_strategy']
```

Новый перевозчик сразу появится в списке и UI.
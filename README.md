### Установка и запуск проекта:

```sh
make install
```
### Запуск тестов:

```sh
make tests
```

### Обновление форматов налоговых номеров:
Чтобы обновить форматы налоговых номеров необходимо изменить/добавить формат
в файл `src/backend/data/tax_number_format.json`
и выполниить команду:

```sh
make sf c=load:tax-number:format
```
или зайти в php контейнер с помощью команды:
```sh
make sh
```
и выполнить в контейнере команду:
```
bin/console load:tax-number:format 
```

## Запросы:
#### POST для расчёта цены:
http://127.0.0.1:50070/calculate-price

```sh
curl -X POST http://127.0.0.1:50070/calculate-price \
  -H "Content-Type: application/json" \
  -d '{
        "product": 10,
        "taxNumber": "DE123456789",
        "couponCode": "B6"
      }'
```
#### POST для оплаты:
http://127.0.0.1:50070/purchase

```sh
curl -X POST http://127.0.0.1:50070/purchase \
  -H "Content-Type: application/json" \
  -d '{
        "product": 1,
        "taxNumber": "DE123456789",
        "paymentProcessor": "paypal",
        "couponCode": "B6"
      }'
```

[![PHP CI](https://github.com/ridouchire/webhoook-telegram-proxy/actions/workflows/php.yml/badge.svg)](https://github.com/ridouchire/webhoook-telegram-proxy/actions/workflows/php.yml)

# webhoook-telegram-proxy

Суть проекта - принять сообщение или алерт от Grafana из сетевого контура, где недоступен TelegramAPI, и отправить его боту.

## Развёртывание
1. Установить `docker` и `docker-compose`
2. `cp .env.dist .env`, отредактировать.
2. `chmod 777 -R data`
3. `docker-compose build`
4. `docker-compose up -d`

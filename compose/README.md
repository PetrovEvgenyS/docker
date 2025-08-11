# Compose-пример
## NGINX proxy + Go backend

### Структура проекта
```
.
├── backend
│   ├── Dockerfile
│   ├── index.html
│   ├── main.go
│   └── styles.css
├── compose.yaml
└── proxy
    └── nginx.conf
```

### [`compose.yaml`](compose.yaml)
```yaml
services:
  proxy:
    image: nginx
    volumes:
      - type: bind
        source: ./proxy/nginx.conf
        target: /etc/nginx/conf.d/default.conf
        read_only: true
    ports:
      - 80:80
    depends_on:
      - backend

  backend:
    build:
      context: backend
```

Файл compose описывает приложение с двумя сервисами: `proxy` и `backend`.
При развертывании приложения docker compose пробрасывает порт 80 контейнера на тот же порт хоста, как указано в файле.
Убедитесь, что порт 80 на хосте не занят.

- `proxy` проксирует HTTP-трафик на `backend:8080`.
- `backend` собирается из `backend/Dockerfile` (multi-stage builds).

### Запуск

```bash
docker compose up -d
```

Убедитесь, что порт 80 на хосте свободен.

### Проверка
```bash
docker compose ps
NAME                IMAGE             COMMAND                  SERVICE   CREATED          STATUS          PORTS
compose-backend-1   compose-backend   "/quote-generator"       backend   15 minutes ago   Up 15 minutes   8080/tcp
compose-proxy-1     nginx             "/docker-entrypoint.…"   proxy     15 minutes ago   Up 15 minutes   0.0.0.0:80->80/tcp, [::]:80->80/tcp
```

Откройте в браузере: `http://localhost` или выполните `curl http://localhost/`

### Остановка и удаление
```bash
docker compose down
```

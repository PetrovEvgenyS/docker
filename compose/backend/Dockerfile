# Используем образ Go для сборки
FROM golang:1.24-alpine AS builder

# Устанавливаем рабочую директорию внутри контейнера
WORKDIR /app

# Копируем весь исходный код в контейнер
COPY . .

# Собираем бинарный файл и копируем все файлы в директорию "distr"
RUN CGO_ENABLED=0 GOOS=linux go build -o quote-generator main.go && \
    mkdir -p /distr && \
    cp /app/quote-generator /distr/ && \
    cp /app/index.html /distr/ && \
    cp /app/styles.css /distr/

# Итоговый образ: минимальный scratch
FROM scratch

# Копируем собранный бинарный файл и необходимые файлы из предыдущего этапа
COPY --from=builder /distr/ /

# Указываем порт, который будет использовать приложение
EXPOSE 8080

# Команда для запуска приложения
CMD ["/quote-generator"]
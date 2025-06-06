# docker_installation_script.sh

## Описание
Этот bash-скрипт автоматизирует установку Docker на различные дистрибутивы Linux. Скрипт определяет ОС и выполняет соответствующую процедуру установки.

## Поддерживаемые дистрибутивы
- Ubuntu
- AlmaLinux (и другие RHEL-совместимые системы)

## Функциональность
- Автоматическое определение ОС
- Установка последней версии Docker Engine
- Настройка репозиториев
- Добавление текущего пользователя в группу docker
- Проверка успешности установки

## Использование

### Установка прав на выполнение
```bash
chmod +x install_docker.sh
```

### Запуск скрипта
```bash
sudo ./install_docker.sh
```

### После установки
Для применения изменений прав выполните:

```bash
newgrp docker
```

Или выйдите и снова войдите в систему.

## Цветовая схема вывода
- Фиолетовый: информационные сообщения
- Зеленый: успешное завершение
- Красный: ошибки

## Особенности работы

### Скрипт автоматически:
- Удаляет старые версии Docker
- Устанавливает необходимые зависимости
- Настраивает официальные репозитории Docker
- Включает автозапуск службы Docker

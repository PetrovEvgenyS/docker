#!/bin/bash

### ЦВЕТА ##
ESC=$(printf '\033') RESET="${ESC}[0m" MAGENTA="${ESC}[35m" RED="${ESC}[31m" GREEN="${ESC}[32m"

### Функции цветного вывода ##
magentaprint() { echo; printf "${MAGENTA}%s${RESET}\n" "$1"; }
errorprint() { echo; printf "${RED}%s${RESET}\n" "$1"; }
greenprint() { echo; printf "${GREEN}%s${RESET}\n" "$1"; }


# ----------------------------------------------------------------------------------------------------


# Проверка запуска через sudo
if [ -z "$SUDO_USER" ]; then
    errorprint "Пожалуйста, запустите скрипт через sudo."
    exit 1
fi

# Определение дистрибутива:
if [ -f /etc/os-release ]; then
    OS=$(awk -F= '/^ID=/{gsub(/"/, "", $2); print $2}' /etc/os-release)
else
    errorprint "Не удалось определить дистрибутив Linux!"
    exit 1
fi

install_docker_ubuntu() {
    magentaprint "Выполняется установка Docker на Ubuntu..."

    # Удаление старых версий docker
    apt -y remove docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc || {
        errorprint "Ошибка при удалении старых версий Docker"
        exit 1
    }

    # Установка необходимых пакетов
    apt -y install apt-transport-https ca-certificates curl gnupg lsb-release expect
    
    # Добавление официального GPG-ключа Docker
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

    # Добавление репозитория Docker в список источников APT
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
         $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

    # Обновление списка пакетов с новым репозиторием Docker
    apt update

    # Установка Docker Engine
    apt -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Добавление пользователя в группу docker
    usermod -aG docker $SUDO_USER

    # Вызов функции finish
    finish
}

install_docker_almalinux() {
    magentaprint "Выполняется установка Docker на Almalinux..."

    # Удаление старых версий Docker, если они установлены
    dnf -y remove docker docker-client docker-client-latest docker-common docker-latest docker-latest-logrotate docker-logrotate docker-engine

    # Установка необходимых пакетов
    dnf -y install yum-utils

    # Добавление репозитория Docker
    yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

    # Установка Docker Engine
    dnf -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Запуск Docker и добавление в автозапуск
    systemctl enable --now docker

    # Добавление пользователя в группу docker
    usermod -aG docker $SUDO_USER

    # Вызов функции finish
    finish
}

finish() {
    magentaprint "Статус службы Docker"
    systemctl status docker --no-page

    magentaprint "Версия Docker"
    docker --version

    greenprint "Docker успешно установлен на $OS."
    magentaprint "Для применения изменений прав пользователя необходимо перелогиниться или выполнить: newgrp docker"
    magentaprint "newgrp docker - позволяет запускать команды Docker без использования sudo и не перелогиниваться."
}

case "$OS" in
    ubuntu)
        install_docker_ubuntu
        ;;
    almalinux)
        install_docker_almalinux
        ;;
    *)
        errorprint "Скрипт не поддерживает установленную операционную систему: $OS"
        exit 1
        ;;
esac

#!/bin/bash

### ЦВЕТА ##
ESC=$(printf '\033') RESET="${ESC}[0m" BLACK="${ESC}[30m" RED="${ESC}[31m"
GREEN="${ESC}[32m" YELLOW="${ESC}[33m" BLUE="${ESC}[34m" MAGENTA="${ESC}[35m"
CYAN="${ESC}[36m" WHITE="${ESC}[37m" DEFAULT="${ESC}[39m"

magentaprint() { printf "${MAGENTA}%s${RESET}\n" "$1"; }
errorprint() { printf "${RED}%s${RESET}\n" "$@"; }
greenprint() { printf "${GREEN}%s${RESET}\n" "$@"; }

# Проверка прав root
if [ "$(id -u)" -ne 0 ]; then
    errorprint "Этот скрипт должен запускаться с правами root или через sudo!"
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

    # Обновление списка пакетов
    apt update

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
}

install_docker_almalinux() {
    magentaprint "Выполняется установка Docker на Almalinux..."

    # Удаление старых версий Docker, если они установлены
    dnf -y remove docker docker-client docker-client-latest docker-common docker-latest docker-latest-logrotate docker-logrotate docker-engine

    # Установка необходимых пакетов
    dnf install -y yum-utils

    # Добавление репозитория Docker
    yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

    # Установка Docker Engine
    dnf -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Запуск Docker и добавление в автозапуск
    systemctl enable --now docker

    # Добавление пользователя в группу docker
    usermod -aG docker $SUDO_USER
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

# Проверка установки
echo " "

if docker --version; then
    greenprint "Docker успешно установлен на $OS."
    magentaprint "Для применения изменений прав пользователя необходимо перелогиниться или выполнить: newgrp docker"
    magentaprint "newgrp docker - позволяет запускать команды Docker без использования sudo и не перелогиниваться."
else
    errorprint "Ошибка установки Docker!"
    exit 1
fi
package main // Определяет основной пакет программы

import (
	"fmt"           // Импортирует пакет для форматирования и вывода текста
	"html/template" // Импортирует пакет для работы с HTML-шаблонами
	"math/rand"     // Импортирует пакет для генерации случайных чисел
	"net"           // Импортирует пакет для работы с сетевыми интерфейсами
	"net/http"      // Импортирует пакет для создания HTTP-сервера
)

const appVersion = "v1.0.0" // Задает версию приложения как константу

var quotes = []string{ // Определяет slice строк с цитатами
	"Жизнь — это путешествие, а не пункт назначения.",               // Цитата 1
	"Лучший способ начать — перестать говорить и начать делать.",    // Цитата 2
	"Счастье — это когда ты делаешь то, что любишь.",                // Цитата 3
	"Каждый день — это новый шанс стать лучше.",                     // Цитата 4
	"Мечты становятся реальностью, когда ты начинаешь действовать.", // Цитата 5
}

func main() { // Основная функция программы
	http.HandleFunc("/", quoteHandler) // Регистрирует обработчик для корневого пути
	http.Handle("/styles.css", http.FileServer(http.Dir(".")))
	//	http.Handle("/static/", http.StripPrefix("/static/", http.FileServer(http.Dir(".")))) // Регистрирует обработчик статических файлов из корня

	fmt.Println("Server started at :8080") // Выводит сообщение о запуске сервера
	http.ListenAndServe(":8080", nil)      // Запускает сервер на порту 8080
}

func quoteHandler(w http.ResponseWriter, r *http.Request) { // Обработчик запросов к корневому пути
	tmpl, err := template.ParseFiles("index.html") // Загружает HTML-шаблон
	if err != nil {                                // Проверяет наличие ошибок при загрузке шаблона
		http.Error(w, "Internal Server Error", http.StatusInternalServerError) // Возвращает ошибку 500
		return                                                                 // Прерывает выполнение
	}

	quote := quotes[rand.Intn(len(quotes))] // Выбирает случайную цитату из среза
	serverIP, _ := getServerIP()            // Получает IP-адрес сервера
	if serverIP == "" {                     // Проверяет, если IP не найден
		serverIP = "Unknown" // Устанавливает значение "Unknown"
	}

	data := struct { // Определяет анонимную структуру для передачи данных в шаблон
		Quote      string // Поле для цитаты
		AppVersion string // Поле для версии приложения
		ServerIP   string // Поле для IP-адреса
	}{ // Инициализирует структуру
		Quote:      quote,      // Присваивает цитату
		AppVersion: appVersion, // Присваивает версию
		ServerIP:   serverIP,   // Присваивает IP
	}

	tmpl.Execute(w, data) // Рендерит шаблон HTML с данными
}

func getServerIP() (string, error) { // Функция для получения IP-адреса сервера
	addrs, err := net.InterfaceAddrs() // Получает список сетевых адресов
	if err != nil {                    // Проверяет наличие ошибок
		return "", err // Возвращает пустую строку и ошибку
	}

	for _, addr := range addrs { // Перебирает адреса
		if ipnet, ok := addr.(*net.IPNet); ok && !ipnet.IP.IsLoopback() && ipnet.IP.To4() != nil { // Проверяет, является ли адрес IPv4 и не локальным
			return ipnet.IP.String(), nil // Возвращает IP-адрес как строку
		}
	}
	return "", nil // Возвращает пустую строку, если IP не найден
}

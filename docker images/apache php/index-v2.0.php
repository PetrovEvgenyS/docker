<?php
// Запускаем сессию для хранения сообщений между запросами
session_start();

// Получаем IP-адрес сервера из глобального массива $_SERVER
$ip_server = $_SERVER['SERVER_ADDR'];

// Параметры подключения к базе данных PostgreSQL
$host = "postgres-clusterip";       // Адрес хоста базы данных
$port = "5432";                     // Порт для подключения
$dbname = "demo";                   // Название базы данных
$user = "demo";                     // Имя пользователя базы данных
$password = "demo";                 // Пароль пользователя базы данных

// Устанавливаем соединение с базой данных
$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Проверяем, успешно ли установлено соединение
if (!$dbconn) {
    echo "<p class='error'>Ошибка подключения к базе данных!</p>";
    exit; // Прерываем выполнение скрипта в случае ошибки
}

// Обрабатываем данные формы, если запрос методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем и очищаем данные из формы от лишних пробелов
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password_input = trim($_POST["password"]);

    // Проверяем, заполнены ли все поля
    if (empty($username) || empty($email) || empty($password_input)) {
        // Сохраняем сообщение об ошибке в сессии
        $_SESSION['message'] = "<p class='error'>Все поля обязательны для заполнения!</p>";
    } else {
        // Хешируем пароль для безопасного хранения
        $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
        
        // Подготовленный SQL-запрос для вставки данных пользователя
        $query = "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)";
        
        // Выполняем запрос с параметрами для защиты от SQL-инъекций
        $result = pg_query_params($dbconn, $query, array($username, $email, $hashed_password));

        if ($result) {
            // Если регистрация успешна, сохраняем сообщение об успехе
            $_SESSION['message'] = "<p class='success'>Пользователь успешно зарегистрирован!</p>";
            // Перенаправляем на ту же страницу для очистки формы
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Получаем текст последней ошибки базы данных
            $error = pg_last_error($dbconn);
            if (strpos($error, 'duplicate key') !== false) {
                // Если email уже существует в базе данных
                $_SESSION['message'] = "<p class='error'>Этот email уже зарегистрирован!</p>";
            } else {
                // Другая ошибка при регистрации
                $_SESSION['message'] = "<p class='error'>Ошибка регистрации: $error</p>";
            }
        }
    }
    // Перенаправляем на ту же страницу после обработки формы
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Закрываем соединение с базой данных
pg_close($dbconn);
?>

<!DOCTYPE html>     <!-- Объявление типа документа HTML5 -->
<html>              <!-- Начало HTML-документа -->
<head>              <!-- Начало секции заголовка документа -->
    <title>Kubernetes Hello Page</title>    <!-- Установка заголовка страницы, отображается во вкладке браузера -->
    <style>                                 /* Начало секции встроенных CSS-стилей */
        body {                              /* Стили для основного элемента body */
            background: linear-gradient(to right, #cd77e2, #7ab4e7); /* Градиентный фон от розово-фиолетового к голубому */
            font-family: Arial, sans-serif; /* Установка шрифта Arial с запасным вариантом sans-serif */
            margin: 0;                      /* Убирает внешние отступы по умолчанию */
            padding: 20px;                  /* Внутренний отступ 20px со всех сторон */
            display: flex;                  /* Использование flexbox для центрирования содержимого */
            justify-content: center;        /* Горизонтальное центрирование элементов */
            align-items: center;            /* Вертикальное центрирование элементов */
            min-height: 100vh;              /* Минимальная высота равна 100% высоты видимой области */
        }
        
        .container {                    /* Стили для контейнера с классом "container" */
            background-color: white;    /* Белый фон контейнера */
            padding: 20px 40px;         /* Внутренние отступы: 20px сверху/снизу, 40px слева/справа */
            border-radius: 10px;        /* Скругленные углы радиусом 10px */
            box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Тень вокруг контейнера с прозрачностью 0.1 */
            text-align: center;         /* Центрирование текста внутри контейнера */
        }
        
        h1 {                          /* Стили для заголовков первого уровня */
            color: #2c3e50;         /* Темно-синий цвет текста */
            margin-bottom: 20px;      /* Нижний внешний отступ 20px */
        }
        
        p {                       /* Стили для параграфов */
            margin: 10px 0;       /* Внешние отступы: 10px сверху/снизу, 0 слева/справа */
            color: #34495e;     /* Серо-синий цвет текста */
        }
        
        .author {                 /* Стили для элементов с классом "author" */
            color: #2980b9;     /* Голубой цвет текста */
            font-weight: bold;    /* Жирное начертание текста */
            font-style: italic;   /* Курсивное начертание текста */
        }
        
        h3 {                    /* Стили для заголовков третьего уровня */
            color: green;     /* Зеленый цвет текста */
        }
        
        .form-group {             /* Стили для групп элементов формы */
            margin: 15px 0;       /* Внешние отступы: 15px сверху/снизу, 0 слева/справа */
        }
        
        input[type="text"], input[type="email"], input[type="password"] { /* Стили для полей ввода */
            padding: 8px;         /* Внутренний отступ 8px со всех сторон */
            width: 200px;         /* Фиксированная ширина поля 200px */
            border: 1px solid #ccc; /* Серая рамка толщиной 1px */
            border-radius: 5px;   /* Скругленные углы радиусом 5px */
        }
        
        input[type="submit"] {    /* Стили для кнопки отправки формы */
            padding: 10px 20px;   /* Внутренние отступы: 10px сверху/снизу, 20px слева/справа */
            background-color: #2980b9; /* Голубой фон кнопки */
            color: white;         /* Белый цвет текста */
            border: none;         /* Убирает рамку по умолчанию */
            border-radius: 5px;   /* Скругленные углы радиусом 5px */
            cursor: pointer;      /* Курсор в виде указателя при наведении */
        }
        
        input[type="submit"]:hover { /* Стили кнопки при наведении курсора */
            background-color: #2c3e50; /* Темно-синий фон при наведении */
        }
        
        .error {                /* Стили для текста ошибок */
            color: red;       /* Красный цвет текста */
        }
        
        .success {              /* Стили для текста успешных сообщений */
            color: green;     /* Зеленый цвет текста */
        }
    </style> <!-- Конец секции стилей -->
</head> <!-- Конец секции заголовка -->
<body> <!-- Начало тела документа -->
    <div class="container"> <!-- Контейнер для содержимого с классом "container" -->
        <h1>Hello From Kubernetes</h1> <!-- Заголовок первого уровня -->
        <p>Server IP Address is: <?php echo $ip_server; ?></p> <!-- Параграф с IP-адресом сервера -->
        <p>Made by <span class='author'>Evgeny Petrov</span></p> <!-- Параграф с именем автора -->
        <h3>Version 1</h3> <!-- Заголовок третьего уровня с версией -->

        <!-- Выводим сообщение из сессии, если оно есть -->
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message']; // Вывод сообщения
            unset($_SESSION['message']); // Удаляем сообщение после вывода
        }
        ?>

        <h2>Регистрация</h2> <!-- Заголовок второго уровня для формы -->
        <form method="POST" action=""> <!-- Форма с методом POST и обработкой на этой же странице -->
            <div class="form-group"> <!-- Группа элементов формы для имени пользователя -->
                <input type="text" name="username" placeholder="Имя пользователя" required> <!-- Поле ввода имени -->
            </div>
            <div class="form-group"> <!-- Группа элементов формы для email -->
                <input type="email" name="email" placeholder="Email" required> <!-- Поле ввода email -->
            </div>
            <div class="form-group"> <!-- Группа элементов формы для пароля -->
                <input type="password" name="password" placeholder="Пароль" required> <!-- Поле ввода пароля -->
            </div>
            <div class="form-group"> <!-- Группа элементов формы для кнопки -->
                <input type="submit" value="Зарегистрироваться"> <!-- Кнопка отправки формы -->
            </div>
        </form> <!-- Конец формы -->
    </div> <!-- Конец контейнера -->
</body> <!-- Конец тела документа -->
</html> <!-- Конец HTML-документа -->
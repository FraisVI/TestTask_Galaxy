Описание
Необходимо реализовать REST API для работы с системой игровых рейтингов пользователей. Система позволяет добавлять новых пользователей, начислять им очки рейтинга, получать текущее место пользователя в рейтинге, а также просматривать топ самых рейтинговых пользователей.

Конечные точки
POST /users
Создает нового пользователя с уникальным именем.

Параметр: username

Тип: string

Описание: **Required**  Уникальное имя пользователя. Может состоять только из латинских букв, цифр и нижнего подчёркивания. Максимальная длина 50 символов, минимальная длина 3 символа.
Response example

Response codes
* 201 Created: Пользователь успешно создан.
* 400 Bad Request: Некорректные параметры запроса.
* 409 Conflict: Пользователь с таким именем уже существует.

POST /users/{userId}/score
Добавляет очки пользователю.

Параметр: points

Тип: integer

Описание: **Required** Количество добавляемых очков. Диапазон допустимых значений [1; 10000].

Response codes
* 200 OK: Очки успешно добавлены.
* 400 Bad Request: Некорректные параметры запроса.
* 404 Not Found: Пользователь не найден.

GET /leaderboard/top
Возвращает топ-10 пользовательского рейтинга.

Параметр: period

Тип: string

Описание: Период времени для подсчёта рейтинга. Допустимые значения: day, week, month. По умолчанию принимает значение day.

Response example

{
"period": "week",
"top": [
{ "position": 1, "user_id": 12, "username": "john", "score": 500 },
{ "position": 2, "user_id": 8, "username": "alexia", "score": 450 },
// ...
{ "position": 10, "user_id": 23, "username": "user1999", "score": 150 }
]
}

Response codes
* 200 OK: Запрос выполнен успешно.
* 400 Bad Request: Некорректные параметры запроса.

GET /leaderboard/rank/{$userId}
Возвращает место пользователя в рейтинге.

Параметр: period
Тип: string
Описание: Период времени для подсчёта места в рейтинге. Допустимые значения: day, week, month. По умолчанию принимает значение day.

Response example

{
"user_id": 1,

"period": "week",

"score": 500,

"rank": 39
}

Response codes
* 200 OK: Запрос выполнен успешно.
* 400 Bad Request: Некорректные параметры запроса.
* 404 Not Found: Пользователь не найден.

Технические требования
Для оптимизации производительности при расчёте рейтинга допускается использование любых средств.

Требуется таблица с логом добавления очков пользователям.

Основные технологии:

PHP, любой фреймворк

MySQL

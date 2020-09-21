# Тестовое Задание
1. На своем сервере или виртуальной машине установить Phalcon framework 4;
2. Создать таблицу с полями: Фамилия Имя Отчество и заполнить произвольными данными, не менее 1000 строк;
3. С помощью Phalcon создать REST Webservice c методами:
                3.1. Чтение записей из таблицы;
                3.2. Добавление записей в таблицу;
                3.3. Изменение записей в таблице;
                3.4. Удаление записей в таблице;
4. На отдельной странице вывести интерфейс для работы с таблицей;
5. Реализовать полнотекстовый поиск по данным из таблицы

## Инструкции для тестрирования
1.по умолчанию инстанс подключается к базе:

Конфиг подключения к базе лежит в 
>app/config/config.php

'host'     => 'localhost',

'username' => 'docker',

'password' => '1111',

'dbname'   => 'test',

Настройки базы не выносил в отдельный конфиг лежит в index.php

2.запрос для импорта таблицы лежит в файле contacts.sql

3.С помощью Phalcon создать REST Webservice c методами:

3.1. Чтение записей из таблицы

> (всех запией) curl --location --request GET 'http://0.0.0.0/api/contacts/'

> (конкретной записи) GET curl --location --request GET 'http://0.0.0.0/api/contacts/{id}'

3.2. Добавление записей в таблицу;

> curl --location --request POST 'http://0.0.0.0/api/contactadd' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'data={"lastName":"Sysykin,"firstName":"Sysyck","middleName":"Sysykovich"}'

3.3. Изменение записей в таблице;

> curl --location --request PUT 'http://0.0.0.0/api/contact/{id}'  --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'data={"lastName":"Sysykin11","firstName":"Sysyck","middleName":"Sysykovich"}'

3.4. Удаление записей в таблице;

>> curl -i -X DELETE http://0.0.0.0/api/contact/{id}


4.На отдельной странице вывести интерфейс для работы с таблицей;

>Выведено на главную страницу веб сервиса

5.Реализовать полнотекстовый поиск по данным из таблицы

>Реализовано на главной странице.

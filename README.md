# Месячный проект:"Магазин спортивной обуви (Web)"

Мой сайт поднятый на хостинге:https://timeweb.com/

Мой сайт:https://sneakershop.webtm.ru/

# На моем сайте присутствует:

1.Форма авторизации/регистрации пользователя

2.Система добавления/удаления товара (корзине, избранное)

3.Система оформления заказа и отображения истории заказа в профиле.

# Мой стек проекта
### **Frontend**
**HTML/CSS** – Для создания пользовательского интерфейса и стилей.
### Backend
**PHP**: Язык программирования для серверной разработки.
### База данных
**MySQL** Реляционная база данных для хранения информации.
### Сервер
**Apache** Веб-сервер, который обрабатывает HTTP-запросы и отдает HTML-страницы и другие ресурсы клиентам.
### Администрирование базы данных
**phpMyAdmin** Инструмент для администрирования базы данных MySQL через веб-интерфейс.

# База данных моего проекта
 ```sql
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
```

 ```sql
CREATE TABLE IF NOT EXISTS `order_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `date_time` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_email` (`user_email`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
```

 ```sql
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;
```

 ```sql
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
```

 ```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmed_password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;
```





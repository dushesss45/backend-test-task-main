create table if not exists products
(
    id int auto_increment primary key,
    uuid  varchar(255) not null comment 'UUID товара',
    category  varchar(255) not null comment 'Категория товара',
    is_active tinyint default 1  not null comment 'Флаг активности',
    name varchar(255) not null comment 'Тип услуги', #Изменено с text на varchar для упрощения той же индексации, например
    description text null comment 'Описание товара',
    thumbnail  varchar(255) null comment 'Ссылка на картинку',
    price decimal(10,2) not null comment 'Цена'#Изменено с float на decimal для точной записи стоимости товара, чтобы избежать в поле более 2 цифр после запятой
)
    comment 'Товары';

create index is_active_idx on products (is_active);

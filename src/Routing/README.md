Routing
=================
Иcпользуется [роутер Symfony](https://github.com/symfony/routing)

Описание маршрутов происходит с помощью аттрибутов, но вместо базового
аттрибута `\Symfony\Component\Routing\Annotation\Route`, лучше использовать
расширенный `\EnjoysCMS\Core\Routing\Annotation\Route` аттрибут.

_Можно в проекте использовать одновременно оба аттрибута, если это не запрещено явно в проекте. Лучше разрешить
поддержку обоих аттрибута._

Он поддерживает весь функционал стандартного symfony аттрибута, но еще добавляет несколько полей:

- `title` - по умолчанию _null_
- `comment` - по умолчанию _null_
- `middlewares` - по умолчанию _[]_
- `needAuthorized` - по умолчанию _true_

__Все поля не обязательны!__

#### Пример описания роутов

1. В обычных роутах

```php
use EnjoysCMS\Core\Routing\Annotation\Route;
#[Route('/page',
    title: 'Страница', 
    comment: 'Комментарий для роута', 
    needAuthorized: false, 
    middlewares: [
        Middleware1::class,
        Middleware2::class,
    ]   
)] 
class Controller 
{
    public function __invoke(){
     //...
    }
}
```

2. В группированных роутах

```php
use EnjoysCMS\Core\Routing\Annotation\Route;
#[Route('/api',
    needAuthorized: false, 
    middlewares: [
        Middleware1::class,
    ]   
)] 
class Controller 
{
    #[Route('/path1', 'path1')] 
    public function controller1(){
     //...
    }
    
    #[Route('/path2', 'path2',
        middlewares: [
             Middleware2::class,
        ]   
    )] 
    public function controller(){
     //...
    }
}
```

Route `path1` будет иметь путь: `/api/path1`, и один middleware (Middleware1), needAuthorized будет false

Route `path2` будет иметь путь: `/api/path2`, и два middleware (Middleware1, Middleware2), needAuthorized будет true,
т.к. он явно переопределен

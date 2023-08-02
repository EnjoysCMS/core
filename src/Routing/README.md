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
- `groups` - по умолчанию _[]_

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

3. Определения `groups` и способ воздействия на роуты с этими метками

```php
use EnjoysCMS\Core\Routing\Annotation\Route;

class Controller 
{
    #[Route('/path1', 'path1',
        groups: [
            'api',
            'group1'
        ]   
    )] 
    public function controller1(){
     //...
    }
    
    #[Route('/path2', 'path2',
        groups: 'api'
    )] 
    public function controller(){
     //...
    }
}
```

Файл настройки роутера (например, _config/routing.yml_)

```yaml
router:
    #...before something...
    groups:
        api:
            middlewares:
                - Middleware1::class
        group1:
            middlewares:
                - Middleware2::class
```

Route `path1` будет иметь два middleware (Middleware1, Middleware2)
Route `path2` будет иметь один middleware (Middleware1)

По-умочанию, разрешено только менять значение параметров у `middlewares`, и `acl`. Остальное будет пропускаться, для
добавления дополнительных параметров нужно изменить настройку `allowed_change_group_options` в _config/routing.yml_

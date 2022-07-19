# Example
Настройки для подключения Locale Storage
```yaml
\EnjoysCMS\Core\StorageUpload\Local:
    rootDirectory: /public/upload
    publicUrl: /upload
    permissionMap:
        dir:
            private: 0755
```

Настройки для подключения Yandex Object Storage
```yaml
\EnjoysCMS\Core\StorageUpload\S3:
    bucket: enjoys
    prefix: catalog # можно не указывать
    clientOptions:
        endpoint: https://storage.yandexcloud.net
        credentials:
            key: ---KEY---
            secret: ---SECRET---
        region: ru
        version: latest
```

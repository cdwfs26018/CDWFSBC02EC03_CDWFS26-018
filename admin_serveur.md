
---

# 📄 `admin_serveur.md`

```md
# Administration serveur – VM de production Symfony

## 🖥️ Environnement serveur

- **OS** : Ubuntu Server 22.04 LTS
- **Type** : Machine virtuelle (VM)
- **Serveur web** : Nginx
- **PHP** : PHP 8.3 (FPM)
- **Base de données** : MariaDB
- **Gestionnaire de dépendances** : Composer

---

## 📦 Installation des services

### Nginx
```bash
sudo apt install nginx

PHP et extensions
PHP et extensions

memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 30
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000

# Налаштування середовища розробки

[← Повернутися до основної документації](../README.md)

---

## Необхідне ПЗ

| ПЗ              | ЛР 1-5 (базове) | ЛР 6-7 (Laravel) |
| --------------- | :-------------: | :--------------: |
| PHP 8.x         |        ✓        |        ✓         |
| Git             |        ✓        |        ✓         |
| Composer        |                 |        ✓         |
| MySQL / MariaDB |                 |        ✓         |

---

## Швидкий старт

Оберіть вашу операційну систему:

- [Windows](#-windows)
- [macOS / Linux](#-macos--linux)

---

## 🪟 Windows

### Базове встановлення (ЛР 1-5)

Відкрийте **PowerShell** та виконайте:

```powershell
cd setup
.\install-basic.ps1
```

### Встановлення для Laravel (ЛР 6-7)

```powershell
cd setup
.\install-laravel.ps1
```

### Повне встановлення (все разом)

```powershell
cd setup
.\install.ps1
```

### ⚠️ Можливі проблеми

**`php` не розпізнається (`The term 'php' is not recognized`):**
PHP не встановлено або не додано до PATH. Використайте один з варіантів нижче (ручне встановлення або скрипт).

**Scoop не встановлюється від адміністратора:**

```
Running the installer as administrator is disabled by default
```

Scoop **не працює** з правами адміністратора. Рішення:
- **Закрийте** PowerShell
- Відкрийте PowerShell **звичайним способом** (без "Запустити від імені адміністратора")
- Повторіть встановлення

**Помилка виконання скриптів:**

```powershell
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
```

**Після встановлення PHP все ще не знаходиться:**
Закрийте **всі** вікна PowerShell/Terminal та відкрийте нове. PATH оновлюється лише для нових вікон.

**"Кракозябри" в консолі:**
Переконайтесь, що файл збережено у кодуванні **UTF-8 без BOM**.

> Більше рішень: [troubleshooting/windows.md](../troubleshooting/windows.md)

### Ручне встановлення

<details>
<summary><b>PHP (без скрипта)</b></summary>

1. Завантажте PHP: [windows.php.net/download](https://windows.php.net/download/) — **VS16 x64 Thread Safe** (zip)
2. Розпакуйте в `C:\php`
3. Додайте `C:\php` до системної змінної **PATH**:
   - Win + R → `sysdm.cpl` → **Додатково** → **Змінні середовища**
   - У **Path** додайте `C:\php`
4. Перевірте: відкрийте нове вікно PowerShell → `php -v`

</details>

<details>
<summary><b>Git (без скрипта)</b></summary>

1. Завантажте Git: [git-scm.com/download/win](https://git-scm.com/download/win)
2. Встановіть з параметрами за замовчуванням
3. Перевірте: `git --version`

</details>

<details>
<summary><b>Composer (для Laravel, ЛР 6-7)</b></summary>

1. Завантажте: [getcomposer.org/download](https://getcomposer.org/download/) — **Composer-Setup.exe**
2. Встановіть (інсталятор сам знайде PHP)
3. Перевірте: `composer -V`

</details>

### Альтернативні варіанти для Windows

<details>
<summary><b>WSL (Windows Subsystem for Linux) — рекомендовано</b></summary>

```powershell
wsl --install
```

Після перезавантаження відкрийте Ubuntu та використовуйте bash скрипти (див. macOS / Linux).

</details>

<details>
<summary><b>XAMPP</b></summary>

1. Завантажте [XAMPP](https://www.apachefriends.org/download.html)
2. Встановіть з компонентами: Apache, MySQL, PHP
3. Додайте PHP до PATH: `C:\xampp\php`

</details>

---

## 🍎 macOS / Linux

### Базове встановлення (ЛР 1-5)

```bash
cd setup
chmod +x install-basic.sh
./install-basic.sh
```

### Встановлення для Laravel (ЛР 6-7)

```bash
cd setup
chmod +x install-laravel.sh
./install-laravel.sh
```

### Повне встановлення (все разом)

```bash
cd setup
chmod +x install.sh
./install.sh
```

### Ручне встановлення

<details>
<summary><b>macOS (Homebrew)</b></summary>

```bash
# Базове
brew install php git

# Для Laravel
brew install composer mysql
brew services start mysql
```

</details>

<details>
<summary><b>Ubuntu / Debian</b></summary>

```bash
# Базове
sudo apt update
sudo apt install -y php php-cli php-mbstring php-xml php-curl git

# Для Laravel
sudo apt install -y composer mariadb-server mariadb-client php-mysql php-zip
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

</details>

<details>
<summary><b>Fedora / RHEL</b></summary>

```bash
# Базове
sudo dnf install -y php php-cli php-mbstring php-xml php-curl git

# Для Laravel
sudo dnf install -y composer mariadb-server mariadb php-mysql php-zip
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

</details>

---

## Перевірка встановлення

```bash
# Базове
php -v          # PHP 8.x
git --version   # git version 2.x

# Laravel
composer -V     # Composer version 2.x
mysql --version # mysql Ver 8.x або MariaDB
```

---

## Запуск проєкту

Див. [docs/running-project.md](../docs/running-project.md)

# Windows: Типові проблеми

[← Налаштування середовища](../setup/README.md)

---

## `php` не розпізнається

```
php : The term 'php' is not recognized as the name of a cmdlet, function, script file, or operable program.
```

**Причина:** PHP не встановлено або не додано до PATH.

**Рішення (оберіть одне):**

### Спосіб 1: Через Scoop (рекомендовано)

> **Важливо:** відкрийте PowerShell **БЕЗ** прав адміністратора!

```powershell
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser -Force
irm get.scoop.sh | iex
scoop install php git
```

Закрийте та відкрийте PowerShell заново, перевірте:

```powershell
php -v
```

### Спосіб 2: Завантажити PHP вручну

1. Перейдіть на [windows.php.net/download](https://windows.php.net/download/)
2. Завантажте **VS16 x64 Thread Safe** (zip-архів)
3. Розпакуйте в `C:\php`
4. Додайте `C:\php` до PATH:
   - Натисніть **Win + R** → введіть `sysdm.cpl` → Enter
   - Вкладка **Додатково** → кнопка **Змінні середовища**
   - У списку **Системні змінні** знайдіть `Path` → **Змінити**
   - Натисніть **Створити** → введіть `C:\php`
   - Натисніть **OK** у всіх вікнах
5. Закрийте **всі** вікна PowerShell та відкрийте нове
6. Перевірте: `php -v`

### Спосіб 3: Через XAMPP

1. Завантажте [XAMPP](https://www.apachefriends.org/download.html)
2. Встановіть з компонентами: PHP
3. Додайте `C:\xampp\php` до PATH (див. крок 4 у Способі 2)

---

## Scoop: "Running the installer as administrator is disabled"

```
Running the installer as administrator is disabled by default, see https://github.com/ScoopInstaller/Install#for-admin for details.
Abort.
```

**Причина:** PowerShell запущено з правами адміністратора, або система вимагає адмін-прав.

**Рішення (оберіть одне):**

### Спосіб 1: Відкрити PowerShell без адмін-прав

1. **Закрийте** поточне вікно PowerShell
2. Натисніть **Win** → введіть `PowerShell` → натисніть **Enter** (НЕ "Запустити від імені адміністратора")
3. Повторіть команди встановлення

> Якщо ви в терміналі VS Code — перевірте, що VS Code запущено **не** від імені адміністратора.

### Спосіб 2: Встановити з прапорцем `-RunAsAdmin`

Якщо спосіб 1 не допоміг — дозвольте Scoop встановитись з адмін-правами:

```powershell
iex "& {$(irm get.scoop.sh)} -RunAsAdmin"
```

Потім продовжуйте:

```powershell
scoop install php git
```

---

## Scoop: помилка завантаження 7zip або інших пакетів

```
The remote name could not be resolved: 'www.7-zip.org'
URL https://www.7-zip.org/a/7z2501-x64.msi is not valid
```

**Причина:** DNS не може знайти сервер. Тимчасова проблема мережі або блокування провайдером.

**Рішення:**

1. Перевірте інтернет-з'єднання (відкрийте будь-який сайт у браузері)
2. Спробуйте ще раз через кілька хвилин:

```powershell
scoop install 7zip
```

3. Якщо не допомагає — змініть DNS на Google:
   - Натисніть **Win + R** → введіть `ncpa.cpl` → **Enter**
   - Правою кнопкою на ваше з'єднання → **Властивості**
   - Виберіть **Internet Protocol Version 4 (TCP/IPv4)** → **Властивості**
   - Оберіть **Використовувати такі адреси DNS-серверів:**
     - Основний: `8.8.8.8`
     - Додатковий: `8.8.4.4`
   - **OK** → повторіть `scoop install 7zip`

4. Після встановлення 7zip — доставте git:

```powershell
scoop install git
```

> **Важливо:** якщо git не встановився разом з php — його потрібно доставити окремо після вирішення проблеми.

---

## Після встановлення PHP все ще не знаходиться

**Причина:** PATH оновлюється лише для нових вікон терміналу.

**Рішення:**

1. Закрийте **всі** вікна PowerShell / CMD / Terminal
2. Відкрийте нове вікно PowerShell
3. Введіть `php -v`

Якщо не допомогло — перезавантажте комп'ютер.

---

## Кирилиця в імені користувача (`C:\Users\АЛЮСЬКА`)

Якщо шлях до вашого профілю містить кирилицю, Scoop може працювати некоректно.

**Рішення:** встановіть PHP вручну (Спосіб 2 вище) замість Scoop.

---

## `git` не розпізнається

```
git : The term 'git' is not recognized as the name of a cmdlet...
```

**Причина:** Git не встановлено або не додано до PATH.

**Рішення (оберіть одне):**

### Через Scoop (якщо вже встановлений)

```powershell
scoop install git
```

### Встановити окремо

1. Завантажте [git-scm.com/download/win](https://git-scm.com/download/win)
2. Встановіть з усіма налаштуваннями за замовчуванням
3. Закрийте **всі** вікна терміналу та відкрийте нове
4. Перевірте: `git --version`

---

## Порт зайнятий (Address already in use)

```
Failed to listen on localhost:8000 (reason: Address already in use)
```

**Причина:** інша програма (або попередній сервер) вже використовує порт 8000.

**Рішення:**

### Спосіб 1: Використати інший порт

```powershell
php -S localhost:8080
```

Відкрийте: `http://localhost:8080`

### Спосіб 2: Знайти і закрити процес

```powershell
netstat -ano | findstr :8000
taskkill /PID <номер_процесу> /F
```

> Якщо не впевнені — просто використайте інший порт (8080, 8888, 3000).

---

## VS Code: термінал не знаходить php або git

**Причина:** VS Code використовує свій термінал, який може не бачити нові змінні PATH.

**Рішення:**

1. **Закрийте VS Code повністю** (не тільки термінал, а весь VS Code)
2. Відкрийте VS Code заново
3. Відкрийте термінал: **Ctrl + `** (або меню Terminal → New Terminal)
4. Перевірте: `php -v` та `git --version`

Якщо не допомогло:

1. Перезавантажте комп'ютер
2. Відкрийте VS Code
3. Спробуйте ще раз

> VS Code підхоплює PATH при запуску. Якщо PHP/Git встановили після відкриття VS Code — він не знає про них.

---

## Перевірка що все працює

```powershell
php -v          # має показати PHP 8.x
git --version   # має показати git version 2.x
php -S localhost:8000   # запуск сервера
```

Відкрийте у браузері: http://localhost:8000

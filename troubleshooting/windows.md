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

**Причина:** PowerShell запущено з правами адміністратора. Scoop за замовчуванням забороняє це.

**Рішення:**

1. **Закрийте** поточне вікно PowerShell
2. Натисніть **Win** → введіть `PowerShell` → натисніть **Enter** (НЕ "Запустити від імені адміністратора")
3. Повторіть команди встановлення

> Якщо ви в терміналі VS Code — перевірте, що VS Code запущено **не** від імені адміністратора.

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

## Перевірка що все працює

```powershell
php -v          # має показати PHP 8.x
git --version   # має показати git version 2.x
php -S localhost:8000   # запуск сервера
```

Відкрийте у браузері: http://localhost:8000

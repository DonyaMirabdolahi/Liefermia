### 🧪 معرفی پروژه

**LiefermiA** یک پلتفرم مدرن سفارش غذای آنلاین در کشور آلمان است که به رستوران‌ها امکان می‌دهد منوی خود را دیجیتال کرده و تجربه‌ای سریع، ساده و شخصی‌سازی‌شده را به مشتریان ارائه دهند.

این پروژه با هدف ارزیابی توانمندی‌های شما در طراحی رابط کاربری (UI/UX) و پیاده‌سازی منطق انتخاب محصول و افزونه‌ها طراحی شده است.

---

### 🎯 هدف پروژه

شما باید یک رابط کاربری تعاملی برای سفارش غذا طراحی و پیاده‌سازی کنید که در آن:

* نمایش لیست محصولات به صورت حرفه‌ای و کاربرپسند
* امکان انتخاب آیتم، سایز، و افزونه‌ها توسط کاربر
* بارگذاری رول‌ها و افزونه‌ها بر اساس سایز انتخاب‌شده
* اعمال محدودیت‌ها با استفاده از:

  * نوع انتخاب: `dropdown` یا `checkbox`
  * تعداد مجاز: `max_option`
  * افزونه‌های رایگان: `free_options`
* در صورت وجود `item.max_option`، مجموع افزونه‌های انتخاب‌شده نباید بیشتر از آن مقدار شود
* محاسبه و نمایش قیمت نهایی براساس سایز، تعداد و افزونه‌ها
* قابلیت تعیین تعداد هر آیتم توسط کاربر

> در نهایت، خروجی باید به صورت JSON معتبر به مسیر زیر ارسال شود:

```
POST /items
```

---

### 🔍 ساختار داده

هر محصول دارای ویژگی‌های زیر است:

* سایزهای مختلف (Small، Medium، Large، XLarge) با قیمت خاص
* رول‌ها و افزونه‌های مرتبط با هر سایز
* افزونه‌هایی با قیمت‌های متفاوت
* محدودیت‌های انتخاب بر اساس قوانین موجود در هر rule

---

### 📜 جدول قوانین انتخاب افزونه‌ها

| ویژگی                       | توضیح                                         |
| --------------------------- | --------------------------------------------- |
| `field_type = dropdown`     | فقط یک گزینه قابل انتخاب است                  |
| `field_type = checkbox`     | چند گزینه قابل انتخاب تا سقف `max_option`     |
| `guard_name = free_options` | افزونه‌ها رایگان هستند (قیمت = ۰)             |
| `guard_name = max_options`  | حداکثر تعداد انتخاب در آن رول را تعیین می‌کند |
| `item.max_option`           | محدودیت مجموع افزونه‌های انتخابی برای آیتم    |

---

### 📤 ارسال داده

هنگام ارسال به API:

```
POST /items
```

داده ارسالی باید شامل موارد زیر باشد:

* شناسه آیتم
* سایز انتخاب‌شده
* رول‌ها و افزونه‌های انتخاب‌شده
* تعداد سفارش
* قیمت نهایی (اختیاری ولی توصیه‌شده)

---

### 🛠 راه‌اندازی پروژه

#### پیش‌نیازها:

* PHP ^8.4
* Node.js & NPM
* Composer
  [https://getcomposer.org/download/](https://getcomposer.org/download/)

#### نصب و اجرا:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

✅ اجرای لوکال:

```bash
php artisan serve
```

✅ اجرای سریع (فقط در Linux):

```bash
php artisan octane:start --port=8000
```

---

### ✅ نکات ارزیابی

* طراحی واکنش‌گرا و حرفه‌ای
* پیاده‌سازی دقیق منطق رول‌ها و قوانین
* کنترل صحیح محدودیت‌ها
* ساختار JSON تمیز و قابل فهم
* کدنویسی منظم و قابل نگهداری

---

با آرزوی موفقیت
**(تیم توسعه LiefermiA)**

---


### 🧪 Project Overview

**LiefermiA** is a modern food delivery platform based in Germany. It helps restaurants digitize their menus and offers customers a fast, simple, and personalized ordering experience.

This challenge evaluates your skills in building UI components and handling complex product customization logic.

---

### 🎯 Project Objective

Build an interactive and responsive food ordering UI that allows users to:

* View and select products from a clean product list
* Select the desired size of each item (Small, Medium, Large, XLarge)
* Dynamically load rules and extras based on the selected size
* Apply rule-based logic, including:

  * Selection type: `dropdown` (single) or `checkbox` (multi)
  * Maximum allowed selections (`max_option`)
  * Free extras (`free_options`)
* Enforce the item-level `max_option` if provided (limit total selected extras)
* Accurately calculate and display the total price based on size, quantity, and extras
* Allow users to choose the quantity of each item

> The final selection must be submitted as a **valid JSON** object to the following endpoint:

```
POST /items
```

---

### 🔍 Data Structure

Each item includes:

* Multiple sizes (Small, Medium, Large, XLarge) with different base prices
* Associated rules and extras per size
* Extras with varying prices per size
* Guard logic to enforce selection constraints

---

### 📜 Rule Types

| Property                    | Description                                      |
| --------------------------- | ------------------------------------------------ |
| `field_type = dropdown`     | Only one option can be selected                  |
| `field_type = checkbox`     | Multiple selections allowed (up to `max_option`) |
| `guard_name = free_options` | Extras are free (price = 0)                      |
| `guard_name = max_options`  | Limits the number of selectable extras           |
| `item.max_option`           | Limits the total number of selected extras       |

---

### 📤 Data Submission

When sending the data:

```
POST /items
```

Payload should include:

* `item_id`
* selected `size`
* selected rules and extras
* quantity of the item
* total price (optional but recommended)

---

### 🛠 Project Setup

#### Requirements:

* PHP ^8.4
* Node.js & NPM
* Composer
  [https://getcomposer.org/download/](https://getcomposer.org/download/)

#### Installation:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

✅ Local Run:

```bash
php artisan serve
```

✅ Octane (Linux only):

```bash
php artisan octane:start --port=8000
```

---

### ✅ Evaluation Criteria

* Responsive and modern UI
* Correct implementation of rule logic
* Proper handling of constraints and validations
* Clean, readable, and structured JSON output
* Maintainable and optimized code

---

Good luck!
**(LiefermiA Development Team)**

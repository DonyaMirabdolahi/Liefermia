# LiefermiA - Pizza Ordering App

## 🍕 About

**LiefermiA** is a modern pizza ordering application built with Laravel backend and React frontend. This project demonstrates a complete food ordering system with dynamic pricing, customizable toppings, and real-time order management.

## 🎯 Features

-   **Interactive Pizza Selection**: Browse available pizzas with images and descriptions
-   **Dynamic Size Selection**: Choose from Small, Medium, Large, and XLarge sizes
-   **Customizable Toppings**: Select from various categories (Cheese, Meats, Vegetables, Premium)
-   **Smart Rule System**:
    -   Dropdown selection for single choices
    -   Checkbox selection for multiple choices
    -   Maximum selection limits
    -   Free topping options
-   **Real-time Price Calculation**: Automatic price updates based on selections
-   **Quantity Management**: Adjust order quantities
-   **Order Submission**: Complete order process with detailed information

## 🛠 Technology Stack

### Backend

-   **Laravel 12** - PHP framework
-   **MySQL** - Database
-   **Eloquent ORM** - Database management
-   **API Resources** - Data transformation

### Frontend

-   **React** - User interface
-   **Vite** - Build tool
-   **Tailwind CSS** - Styling
-   **Axios** - HTTP client

## 📋 Requirements

-   PHP ^8.4
-   Node.js & NPM
-   Composer
-   MySQL

## 🚀 Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/DonyaMirabdolahi/Liefermia.git
    cd Liefermia
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Database setup**
    ```bash
    php artisan migrate --seed
    ```

## 🏃‍♂️ Running the Application

### Backend (Laravel)

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### Frontend (React/Vite)

```bash
npm run dev
```

The frontend will be available at `http://localhost:5173`

## 📊 Database Structure

### Core Models

-   **Items** - Pizza products with images and descriptions
-   **Sizes** - Available sizes (Small, Medium, Large, XLarge)
-   **Extras** - Toppings and add-ons
-   **Rules** - Selection rules and constraints
-   **Users** - User management

### Relationships

-   Items ↔ Sizes (with pricing)
-   Items ↔ Extras (available toppings)
-   Items ↔ Rules (selection constraints)
-   Rules ↔ Extras (available options)
-   Extras ↔ Sizes (pricing per size)

## 🔧 API Endpoints

### Items

-   `GET /api/items` - List all available pizzas
-   `GET /api/items/{id}/details` - Get detailed pizza information
-   `POST /items` - Submit order

### Order Data Structure

```json
{
    "item_id": 1,
    "item_name": "Margherita",
    "size": {
        "id": 2,
        "name": "Medium",
        "price": 12.99
    },
    "quantity": 2,
    "extras": [
        {
            "rule_id": 1,
            "rule_name": "Choose Your Cheese",
            "extra_id": 3,
            "extra_name": "Parmesan",
            "extra_price": 1.5
        }
    ],
    "total_price": 28.98
}
```

## 🎨 UI Components

-   **Product Grid** - Display available pizzas
-   **Modal System** - Detailed product view and customization
-   **Size Selector** - Interactive size buttons
-   **Topping Selector** - Dynamic rule-based selection
-   **Quantity Controls** - Increment/decrement buttons
-   **Price Calculator** - Real-time total display
-   **Success Modal** - Order confirmation

## 🔒 Business Rules

### Selection Constraints

-   **Dropdown Rules**: Single selection only
-   **Checkbox Rules**: Multiple selections up to `max_option`
-   **Free Options**: Zero-cost toppings
-   **Item Limits**: Total extras cannot exceed `item.max_option`

### Pricing Logic

-   Base price from selected size
-   Additional cost for selected extras
-   Quantity multiplier
-   Real-time calculation updates

## 📱 Responsive Design

The application is fully responsive and works on:

-   Desktop computers
-   Tablets
-   Mobile phones

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Developer

**Donya Mirabdolahi**

---

_Built with ❤️ using Laravel and React_

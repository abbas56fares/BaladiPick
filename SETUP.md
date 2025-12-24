# BaladiPick - Delivery Management System

A Laravel 12 web application for managing delivery orders between shops and delivery drivers.

## System Overview

BaladiPick is a delivery management platform with three user roles:

- **Admin**: Full system control, manages shops, delivery users, and monitors all operations
- **Shop**: Creates delivery orders, tracks order status, manages shop profile
- **Delivery**: Accepts orders, handles pickup/delivery with QR and OTP verification

## Features

### Shop Module
- Shop profile management with location (lat/lng)
- Create delivery orders with client details
- Track order status in real-time
- View order history and statistics
- Dashboard with earnings overview

### Delivery Module
- View available orders on map
- Accept and manage delivery orders
- QR code verification for pickup
- OTP generation and verification for delivery
- Earnings tracker and completed deliveries

### Admin Module
- Dashboard with system overview
- Shop management (verify/disable)
- Delivery user management (verify/disable)
- Order monitoring and management
- Reports and CSV export

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Bootstrap 5
- Blade Templates

## Database Schema

- **users**: Admin, shop owners, and delivery drivers
- **shops**: Shop profiles with location data
- **orders**: Delivery orders with status tracking
- **payments**: Payment records (cash)
- **notifications**: System notifications
- **order_logs**: Complete order history audit trail

## Installation

### Prerequisites

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- XAMPP or similar local server

### Setup Steps

1. **Clone or navigate to the project directory**
   ```bash
   cd c:\xampp\htdocs\baladipick
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Copy environment file**
   ```bash
   copy .env.example .env
   ```

4. **Configure database in .env**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=baladipick
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Create database**
   - Open phpMyAdmin
   - Create a new database named `baladipick`

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed database with sample data**
   ```bash
   php artisan db:seed
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    - Open browser: http://localhost:8000

## Default Credentials

After seeding, you can login with these accounts:

**Admin**
- Email: admin@baladipick.com
- Password: password

**Shop Owner**
- Email: shop@baladipick.com
- Password: password

**Delivery Driver**
- Email: delivery@baladipick.com
- Password: password

## Usage Flow

### For Shops

1. Login as shop owner
2. Complete shop profile with location
3. Create delivery orders with client details
4. Monitor order status
5. View statistics and earnings

### For Delivery Drivers

1. Login as delivery
2. Browse available orders
3. Accept an order
4. Scan QR code at pickup
5. Generate OTP for delivery
6. Verify OTP with client
7. Complete delivery

### For Admin

1. Login as admin
2. Verify new shops and delivery drivers
3. Monitor all orders
4. Generate reports
5. Export data as CSV

## Security Features

- Role-based access control (middleware)
- Laravel Policies for authorization
- Password hashing
- Database transactions
- Order status change logging
- CSRF protection

## Order Status Flow

1. **available** - Order created, waiting for delivery driver
2. **pending** - Delivery driver accepted, waiting for pickup
3. **in_transit** - Package picked up (QR verified), on the way
4. **delivered** - Package delivered (OTP verified), completed
5. **cancelled** - Order cancelled by shop or admin

## Contributing

This project was created as an academic project for learning purposes.

## License

This project is open-source and available for educational purposes.

---

**Developed with ❤️ using Laravel 12**

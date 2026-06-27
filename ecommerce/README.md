# ShopZone - Complete E-Commerce Website

## Setup Instructions (XAMPP)

### Step 1: Files Copy Karein
Is `ecommerce` folder ko copy karein aur yahan paste karein:
```
C:/xampp/htdocs/ecommerce/
```

### Step 2: XAMPP Start Karein
- Apache start karein
- MySQL start karein

### Step 3: Database Import Karein
1. Browser mein kholen: `http://localhost/phpmyadmin`
2. "New" par click karein
3. Database name: `ecommerce_db` likhein aur Create karein
4. "Import" tab par jayein
5. `database/schema.sql` file select karein aur Import karein

### Step 4: Website Open Karein
```
http://localhost/ecommerce
```

---

## Login Credentials

### Admin Panel
- URL: `http://localhost/ecommerce/admin`
- Email: `admin@shop.com`
- Password: `admin123`

---

## Project Structure

```
ecommerce/
├── config/
│   └── database.php          # Database connection
├── includes/
│   ├── header.php            # Site header/navigation
│   └── footer.php            # Site footer
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   └── js/
│       └── main.js           # JavaScript
├── ajax/
│   └── cart.php              # Cart AJAX handler
├── admin/
│   ├── index.php             # Dashboard
│   ├── products.php          # Products list
│   ├── add-product.php       # Add product
│   ├── orders.php            # Orders management
│   ├── customers.php         # Customers list
│   └── includes/
│       └── sidebar.php       # Admin sidebar
├── database/
│   └── schema.sql            # Database structure + sample data
├── index.php                 # Homepage
├── products.php              # Products listing
├── product.php               # Product detail
├── cart.php                  # Shopping cart
├── checkout.php              # Checkout
├── order-success.php         # Order confirmation
├── login.php                 # Login page
├── register.php              # Registration
├── orders.php                # My orders
├── contact.php               # Contact page
├── search.php                # Search redirect
└── logout.php                # Logout
```

## Features
- Modern responsive design
- Product listing with filters
- Shopping cart (session-based)
- User registration & login
- Order placement
- Admin dashboard
- Product management
- Order management
- Customer management

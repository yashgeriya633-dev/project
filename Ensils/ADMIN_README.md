# Admin Panel for Ensils - Clay Utensils E-commerce

A comprehensive admin panel for managing the Ensils clay utensils e-commerce website.

## Features

### 🔐 Authentication
- Secure admin login system
- Session management
- Role-based access control

### 📊 Dashboard
- Overview statistics (users, orders, custom requests, products)
- Recent activity feeds
- Quick access to all management sections

### 👥 User Management
- View all registered users
- User details and contact information
- Delete user accounts
- User activity tracking

### 📦 Product Management
- Add new products
- Edit existing products
- Delete products
- Product categorization
- Image management

### 🛒 Order Management
- View all customer orders
- Update order status (pending, processing, shipped, delivered, cancelled)
- Order details and customer information
- Order history tracking

### 🛠️ Custom Requests Management
- View custom product requests from customers
- Update request status
- View reference images uploaded by customers
- Contact customers directly
- Manage custom product specifications

## Installation & Setup

### 1. Database Setup
Run the setup script to create necessary tables and default admin user:

```
http://your-domain/admin_setup.php
```

This will create:
- `admins` table with default admin user
- `products` table with sample products
- `orders` table for order management
- Add status column to existing `custom_products` table

### 2. Default Admin Credentials
- **Username:** `admin`
- **Password:** `admin123`

⚠️ **Important:** Change the default password after first login!

### 3. Access Admin Panel
Navigate to: `http://your-domain/admin_login.php`

## File Structure

```
admin_login.php          - Admin authentication page
admin_dashboard.php      - Main dashboard with statistics
admin_users.php          - User management
admin_products.php       - Product management
admin_orders.php         - Order management
admin_custom_requests.php - Custom requests management
admin_logout.php         - Logout functionality
admin_setup.php          - Database setup script
```

## Database Tables

### admins
- `id` - Primary key
- `username` - Admin username
- `email` - Admin email
- `password` - Hashed password
- `role` - Admin role (admin/super_admin)
- `created_at` - Creation timestamp

### products
- `id` - Primary key
- `name` - Product name
- `price` - Product price
- `description` - Product description
- `image` - Image path
- `category` - Product category
- `created_at` - Creation timestamp

### orders
- `id` - Primary key
- `user_id` - Customer user ID
- `product_name` - Ordered product name
- `quantity` - Order quantity
- `price` - Unit price
- `total_amount` - Total order amount
- `status` - Order status
- `order_date` - Order date
- `shipping_address` - Delivery address
- `customer_name` - Customer name
- `customer_email` - Customer email
- `customer_phone` - Customer phone

### custom_products (existing table)
- All existing columns
- `status` - Request status (pending, processing, completed, cancelled)

## Security Features

- Password hashing using PHP's `password_hash()`
- Session-based authentication
- SQL injection prevention with prepared statements
- Input validation and sanitization
- CSRF protection considerations

## Usage Instructions

### Managing Users
1. Navigate to Users section
2. View user details by clicking the eye icon
3. Edit user information (feature ready for implementation)
4. Delete users if necessary

### Managing Products
1. Go to Products section
2. Click "Add Product" to create new products
3. Fill in product details (name, price, description, image path, category)
4. Edit existing products by clicking the edit button
5. Delete products that are no longer needed

### Managing Orders
1. Access Orders section
2. View order details by clicking the eye icon
3. Update order status using the edit button
4. Track order progress from pending to delivered

### Managing Custom Requests
1. Go to Custom Requests section
2. Review customer specifications
3. View reference images uploaded by customers
4. Update request status as work progresses
5. Contact customers directly via email
6. Delete completed or cancelled requests

## Customization

### Adding New Admin Users
```sql
INSERT INTO admins (username, email, password, role) 
VALUES ('newadmin', 'admin@example.com', 'hashed_password', 'admin');
```

### Modifying Product Categories
Edit the category options in `admin_products.php`:
```php
<option value="NewCategory">New Category</option>
```

### Adding New Order Statuses
Update the ENUM values in the orders table:
```sql
ALTER TABLE orders MODIFY COLUMN status 
ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'new_status');
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in each PHP file
   - Ensure MySQL server is running
   - Verify database name exists

2. **Admin Login Not Working**
   - Run `admin_setup.php` to create admin user
   - Check if admins table exists
   - Verify password hashing

3. **Images Not Displaying**
   - Check image paths in products table
   - Ensure images folder exists and is accessible
   - Verify file permissions

4. **Orders Not Showing**
   - Run `admin_setup.php` to create orders table
   - Check if sample data was inserted
   - Verify table structure

## Support

For technical support or feature requests, please contact the development team.

---

**Note:** This admin panel is designed specifically for the Ensils clay utensils e-commerce website. Make sure to customize it according to your specific business requirements.


# **HealthHub - Medical Booking System**
## 📌 **Overview**

HealthHub is a medical appointment booking system designed to connect patients with doctors. The system allows doctors to manage their schedules, patients to book and track their appointments, and admins to oversee operations.
It includes role-based access:
Doctors can set availability, view appointments, and mark them as completed.
Patients can book appointments, view their booking history, and cancel under certain conditions.
Admins can manage doctors, patients, and system-wide configurations.

## ⚙️ **Prerequisites**

Before running the project, ensure you have the following installed:
- PHP 8.2 or later
- Laravel 10
- Composer
- MySQL 8
- Postman (for API testing)
  
## 📄 **API Documentation**

Detailed API documentation, including request/response examples and authentication methods, is available in the Postman Collection.
- [API Documentation URL:](https://documenter.getpostman.com/view/37675046/2sAYQiCoNF)

## 📥 **Installation**

1. Clone the project:
    + ```
      git clone https://github.com/mohamedabdallah11/HealthHub.git cd HealthHub 
2. Install dependencies:
    + ```
      composer install 
3. Create the environment file:
    + ```
      cp .env.example .env 
4. Update environment variables: Open `.env` and configure the database:
    + ```
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=healthhub
      DB_USERNAME=root
      DB_PASSWORD= 
5. Create the database:
    + ```
      CREATE DATABASE healthhub; 
6. Run migrations:
    + ```
      php artisan migrate  
7. Generate the application key and start the server:
    + ```
      php artisan key:generate 
      php artisan serve

8. run the storage link for uploading images
   + ```
     php artisan storage:link

## 🛠️ **Project Features**

🔐 **Authentication & Authorization**
- Register/login using email and password
- Role-based access control (Admin, Doctor, Client)
- JWT-based authentication for secure API access

📅 **Appointment & Booking System**

- Doctors set their available schedules
- Patients can view available time slots and book appointments
- Doctors can confirm or reject bookings
- Patients can cancel appointments under certain conditions (e.g., not within 2 hours of the appointment)

📊 **Admin Dashboard Features**

- Manage doctors and their specialties
- View all bookings and user activities
- Oversee system-wide reports and statistics

📋 **Doctor Features**

- Manage personal schedules
- View and confirm patient bookings
- Mark appointments as served

🧑‍⚕️ **Patient Features**

- View available doctors and book appointments
- Track booking history (Confirmed, Served, Pending)
- Cancel bookings under defined rules

## 🛍️ **E-Commerce Module**
This module extends HealthHub by adding e-commerce capabilities, allowing users to purchase medical products online.
🔹 Features
- `Product Management:` Admins can add, update, delete, and list products.
- `Order Management:` Users can place orders, and admins can track order status.
- `Shopping Cart:` Users can add/remove products from the cart before checkout.
- `Payment Integration:` Secure payment gateway integration (Stripe, PayPal, etc.).
- `Order History:` Users can view their past purchases.

📥 **Installation for E-Commerce Module**
1. Run migrations for the e-commerce tables:
    + ```
      php artisan migrate --path=database/migrations/ecommerce
2. Seed initial product data (optional):
    + ```
      php artisan db:seed --class=ProductSeeder
3. Clear config cache:
    + ```
      php artisan config:clear

## 🔗 **API Endpoints Overview**

- User Authentication 
- User Authentication With Google 
- Register
- Login
- Logout
- Doctors & Appointments
- Get doctor schedules
- Set availability
- View confirmed & served bookings
- Patient Bookings
- Book an appointment
- Cancel under conditions
- Track booking history
- Admin Controls
- Manage doctors
- Manage specializations
- Manage Profile 
- View system reports

## 📝 **Notes**

- API requests and responses follow a structured format.
- Postman Collection is available for easy API testing.
- The system follows UTC time conversion for accurate appointment tracking.

## 🎯 **Future Enhancements**

- Real-time notifications for doctors and patients
- Reports and analytics for admin users
- Enhanced scheduling features

## 🖊️ **Authors**

👨‍💻 Mohamed Abdallah Hafez
- Role: `Backend Developer`
- 📧 [Email](mohamedabdallahh26@gmail.com)
- 🔗 [LinkedIn](https://www.linkedin.com/in/mohamed-abdallah26/)

👨‍💻 Taha Shosha 
- Role: `Laravel Developer and Web Pen Tester`
- 🔗 [LinkedIn](https://www.linkedin.com/in/taha-shosha-1ba45b233)

🚀 Thank you for using HealthHub!

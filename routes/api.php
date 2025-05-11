<?php

use App\Http\Controllers\API\Authentication\GoogleRoleController;
use App\Http\Controllers\Api\Authentication\OtpController;
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Authentication;
use App\Http\Controllers\Api\Authentication\AuthController;
use App\Http\Controllers\Api\Authentication\socialiteAuthenticationController;
use App\Http\Controllers\Api\Doctor\AppointmentController;
use App\Http\Controllers\Api\Doctor\DoctorController;
use App\Http\Controllers\Api\Doctor\DoctorBookingMangement;
use App\Http\Controllers\Api\Doctor\SpecialtyController;
use App\Http\Controllers\Api\Client\ClientController;
use App\Http\Controllers\API\AdminDashboard\AdminSpecialtyController;

use App\Http\Controllers\E_commerce\ProductController;
use App\Http\Controllers\E_commerce\CategoryController;
use App\Http\Controllers\E_commerce\OrderController;
use App\Http\Controllers\E_commerce\CartController;

use App\Http\Controllers\Api\Booking\BookingController;


Route::middleware(['throttle:apiRateLimit'])->group(function () {

    Route::controller(OtpController::class)->prefix('otp/email/verification')->group(function () {
        Route::post('/send', 'sendOtp');
        Route::post('/verify', 'verifyOtp');
    });
    Route::controller(AuthController::class)->middleware(['verified'])->prefix('otp/password/reset')->group(function () {
        Route::post('/send-otp',  'sendResetOtp');
        Route::post('/verify', 'verifyOtpAndResetPassword');
    });

    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')->middleware(['auth:sanctum', 'role:client,admin,doctor']);
    });
    Route::middleware(['auth:sanctum', 'role:client,doctor,admin'])->prefix('profile')->group(function () {
        Route::get('/show', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'update']);
        Route::put('/changePassword', [ProfileController::class, 'changePassword']);
    });
    Route::controller(socialiteAuthenticationController::class)->prefix('auth')->group(function () {
        Route::get('/google', action: 'redirectToGoogle');
        Route::get('/google/callback', 'handleGoogleCallback');
        Route::post('/google/CompleteRegister', GoogleRoleController::class)->middleware(['auth:sanctum', 'role:deactivated']);
    });
    Route::middleware(['role:doctor', 'auth:sanctum'])->prefix('doctor/appointments')->group(function () {
        Route::post('/store', action: [AppointmentController::class, 'store']);
        Route::get('/show', [AppointmentController::class, 'show']);
        Route::put('/update/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/destroy/{appointment}', [AppointmentController::class, 'destroy']);
        Route::patch('/deactivate/{appointment}', [AppointmentController::class, 'deactivate']);
    });
    Route::middleware(['auth:sanctum', 'role:client,doctor'])->prefix('Doctor/')->group(function () {
        Route::get('/allDoctors', [DoctorController::class, 'allDoctors']);
        Route::get('/showDoctorInfo/{id}', action: [DoctorController::class, 'doctorInformation']);
        Route::get('/searchByName', [DoctorController::class, 'searchByName']);
        Route::get('/filterBySpecialty', [DoctorController::class, 'filterBySpecialty']);
    });

    Route::middleware(['auth:sanctum', 'role:client'])->prefix('Booking')->group(function () {
        Route::post('/bookAppointment', [BookingController::class, 'bookAppointment']);
        Route::patch('/bookAppointment/confirm/{id}', [BookingController::class, 'confirmBooking']);
        Route::delete('/bookAppointment/cancel/{id}', [BookingController::class, 'cancelBooking']);
    });
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')->middleware(['auth:sanctum', 'role:client,admin,doctor']);
    });
    Route::middleware(['auth:sanctum', 'role:client,doctor,admin'])->prefix('profile')->group(function () {
        Route::get('/show', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'update']);
        Route::put('/changePassword', [ProfileController::class, 'changePassword']);
        Route::get('users/slug/{slug}', [ProfileController::class, 'showBySlug']);
    });
    Route::controller(socialiteAuthenticationController::class)->prefix('auth')->group(function () {
        Route::get('/google', action: 'redirectToGoogle');
        Route::get('/google/callback', 'handleGoogleCallback');
        Route::post('/google/CompleteRegister', GoogleRoleController::class)->middleware(['auth:sanctum', 'role:deactivated']);
    });

    Route::middleware(['auth:sanctum', 'role:client,doctor,admin'])->prefix('profile')->group(function () {
        Route::get('/show', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'update']);
        Route::put('/changePassword', [ProfileController::class, 'changePassword']);
    });
    Route::controller(socialiteAuthenticationController::class)->prefix('auth')->group(function () {
        Route::get('/google', action: 'redirectToGoogle');
        Route::get('/google/callback', 'handleGoogleCallback');
        Route::post('/google/CompleteRegister', GoogleRoleController::class)->middleware(['auth:sanctum', 'role:deactivated']);
    });
    Route::middleware(['role:doctor', 'auth:sanctum'])->prefix('doctor/appointments')->group(function () {
        Route::post('/store', action: [AppointmentController::class, 'store']);
        Route::get('/show', [AppointmentController::class, 'show']);
        Route::put('/update/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/destroy/{appointment}', [AppointmentController::class, 'destroy']);
        Route::patch('/deactivate/{appointment}', [AppointmentController::class, 'deactivate']);
    });
    Route::middleware(['auth:sanctum', 'role:client,doctor'])->prefix('Doctor/')->group(function () {
        Route::get('/allDoctors', [DoctorController::class, 'allDoctors']);
        Route::get('/showDoctorInfo/{id}', action: [DoctorController::class, 'doctorInformation']);
        Route::get('/searchByName', [DoctorController::class, 'searchByName']);
        Route::get('/filterBySpecialty', [DoctorController::class, 'filterBySpecialty']);
    });

    Route::middleware(['auth:sanctum', 'role:client,doctor'])->prefix('Booking')->group(function () {
        Route::get('/availableSlots/{appointmentId}', [AppointmentController::class, 'getAvailableSlots']);
        Route::post('/bookAppointment', [BookingController::class, 'bookAppointment']);
        Route::patch('/bookAppointment/confirm/{id}', [BookingController::class, 'confirmBooking']);
        Route::delete('/bookAppointment/cancel/{id}', [BookingController::class, 'cancelBooking']);
    });




    Route::middleware(['auth:sanctum', 'role:doctor'])->prefix('BookingMangement')->group(function () {
        Route::patch('/markBookingAsServed/{id}', [DoctorBookingMangement::class, 'markBookingAsServed']);
        Route::get('/getConfirmedBookings/{appointmentId}', [DoctorBookingMangement::class, 'getConfirmedBookings']);
        Route::get('/getServedBookings/{appointmentId}', [DoctorBookingMangement::class, 'getServedBookings']);
    });

    Route::middleware('auth:sanctum')->prefix('Client')->group(function () {
        Route::get('/bookings/confirmed', [ClientController::class, 'getConfirmedBookings']);
        Route::get('/bookings/served', [ClientController::class, 'getServedBookings']);
        Route::get('/bookings/pending', [ClientController::class, 'getPendingBookings']);
    });


    Route::middleware(['auth:sanctum'])->prefix('Specialties')->group(function () {
        Route::get('/show', [SpecialtyController::class, 'show']);

        Route::middleware(['role:admin'])->group(function () {
            Route::post('/storeSpecialty', [AdminSpecialtyController::class, 'store']);
            Route::put('/updateSpecialty/{id}', [AdminSpecialtyController::class, 'update']);
            Route::delete('/deleteSpecialty/{id}', [AdminSpecialtyController::class, 'destroy']);
        });
    });

    // E-commerce

    Route::middleware(['auth:sanctum'])->prefix('e-commerce')->group(function () {

        // Product Routes (Accessible to Admins Only)
        Route::middleware(['role:admin'])->prefix('products')->group(function () {
            Route::post('/store', [ProductController::class, 'store']);
            Route::post('/{product}/update', [ProductController::class, 'update']);
            Route::delete('/destroy/{product}', [ProductController::class, 'destroy']);
        });

        // Public Product Routes (Accessible to Everyone)
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);

        // Category Routes (Admin Only for Modifications)
        Route::middleware(['role:admin'])->prefix('categories')->group(function () {
            Route::post('/store', [CategoryController::class, 'store']);
            Route::put('/update/{category}', [CategoryController::class, 'update']);
            Route::delete('/destroy/{category}', [CategoryController::class, 'destroy']);
        });

        // Public Category Routes
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);

        // Order Routes (Client Only)
        Route::middleware(['role:client,doctor'])->prefix('orders')->group(function () {
            Route::post('/store', [OrderController::class, 'store']);
            Route::get('/show/{order}', [OrderController::class, 'show']);
            Route::get('/history', [OrderController::class, 'orderHistory']);
        });

        // Order Management Routes (Admin Only)
        Route::middleware(['role:admin'])->prefix('orders')->group(function () {
            Route::get('/all', [OrderController::class, 'index']);
            Route::put('/update-status/{order}', [OrderController::class, 'updateStatus']);
            Route::delete('/destroy/{order}', [OrderController::class, 'destroy']);
        });

        // Cart Routes (Client Only)
        Route::middleware(['role:client,doctor'])->prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            // Modify
            Route::post('/add', [CartController::class, 'store']);
            Route::put('/update/{cart}', [CartController::class, 'update']);
            Route::delete('/destroy/{cart}', [CartController::class, 'destroy']);
            Route::delete('/clear', [CartController::class, 'clear']);
            //checkout
            Route::post('/order-item/{cartItem}', [CartController::class, 'orderSingleItem']);
            Route::post('/order-selected', [CartController::class, 'orderSelectedItems']);
        });
    });

    // Admin Panal
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin/e-commerce')->group(function () {
        // Product Management
        Route::post('/products/store', [ProductController::class, 'store']);
        Route::put('/products/update/{id}', [ProductController::class, 'update']);
        Route::delete('/products/destroy/{id}', [ProductController::class, 'destroy']);

        // Category Management
        Route::post('/categories/store', [CategoryController::class, 'store']);
        Route::put('/categories/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/destroy/{id}', [CategoryController::class, 'destroy']);

        // Order Management
        Route::get('/orders/all', [OrderController::class, 'allOrders']);
        Route::patch('/orders/update-status/{id}', [OrderController::class, 'updateStatus']);
        Route::delete('/orders/destroy/{order}', [OrderController::class, 'destroy']);
    });
});

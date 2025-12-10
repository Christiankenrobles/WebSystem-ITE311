<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Custom routes
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth & Dashboard
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attempt');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Home::dashboard');
$routes->get('/admin/dashboard', 'Home::dashboard');
$routes->get('/teacher/dashboard', 'Home::dashboard');
$routes->get('/admin/materials', 'Home::materialsManagement');
// Registration
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::store');

// Course Enrollment
$routes->get('/courses', 'Course::index');
$routes->get('/course', 'Course::index');
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');
// Course creation (AJAX / form)
$routes->post('/course/create', 'Course::create');

// Materials
$routes->get('/materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('/materials/upload/(:num)', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/materials/list/(:num)', 'Materials::list/$1');

// Notifications
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

// Users Management (Admin)
$routes->get('/users', 'Users::index');
$routes->get('/users/edit/(:num)', 'Users::edit/$1');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->post('/users/update-role', 'Users::updateRole');
$routes->post('/users/create', 'Users::create');
$routes->post('/users/delete/(:num)', 'Users::delete/$1');
$routes->post('/users/delete', 'Users::delete');
$routes->get('/users/search', 'Users::search');

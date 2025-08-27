<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');      // Default homepage
$routes->get('/about', 'Home::about'); // About page
$routes->get('/contact', 'Home::contact'); // Contact page


<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Authentication routes
$routes->post('/login', 'Home::login');
$routes->get('/logout', 'Home::logout');

// Announcement routes
$routes->post('/announcement/create', 'Home::createAnnouncement');
$routes->get('/view/(:num)', 'Home::viewAnnouncement/$1');

// User management routes
$routes->post('/user/create', 'Home::createUser');

// Comment routes
$routes->post('/comment/add', 'Home::addComment');
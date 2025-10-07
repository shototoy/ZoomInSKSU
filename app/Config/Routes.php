<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/login', 'Home::login');
$routes->get('/logout', 'Home::logout');
$routes->post('/post/create', 'Home::createPost');
$routes->post('/announcement/create', 'Home::createAnnouncement');
$routes->get('/view/(:num)', 'Home::viewAnnouncement/$1');
$routes->post('/user/create', 'Home::createUser');
$routes->post('/comment/add', 'Home::addComment');
$routes->post('/reaction/toggle', 'Home::toggleReaction');
$routes->post('/profile/upload', 'Home::uploadProfile');
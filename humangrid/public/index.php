<?php
/**
 * Front Controller
 * HumanGrid - Anti-AI Social Media Platform
 */

// Load configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

// Load core classes
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Controller.php';

// Load controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/PostController.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';

// Initialize router
$router = new Router();

// Public routes
$router->get('/', fn() => (new PostController())->feed());
$router->get('/login', fn() => (new AuthController())->showLogin());
$router->post('/login', fn() => (new AuthController())->login());
$router->get('/register', fn() => (new AuthController())->showRegister());
$router->post('/register', fn() => (new AuthController())->register());
$router->get('/logout', fn() => (new AuthController())->logout());

// Post routes
$router->get('/post/{id}', fn($id) => (new PostController())->show($id));
$router->post('/post/create', fn() => (new PostController())->create());
$router->post('/post/{id}/delete', fn($id) => (new PostController())->delete($id));
$router->post('/like/toggle', fn() => (new PostController())->toggleLike());
$router->post('/post/{id}/comment', fn($id) => (new PostController())->addComment());
$router->post('/post/report', fn() => (new PostController())->report());

// User routes
$router->get('/profile', fn() => (new UserController())->profile());
$router->get('/profile/{username}', fn($username) => (new UserController())->profile($username));
$router->get('/profile/edit', fn() => (new UserController())->editProfile());
$router->post('/profile/update', fn() => (new UserController())->updateProfile());
$router->post('/profile/avatar', fn() => (new UserController())->uploadAvatar());
$router->post('/follow/toggle', fn() => (new UserController())->toggleFollow());
$router->get('/api/search', fn() => (new UserController())->search());

// Dispatch request
$router->dispatch();

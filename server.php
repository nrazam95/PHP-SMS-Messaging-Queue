<?php
// Create RESTful API in PHP with router class

require_once 'router.php';
require 'services/user_service.php';
require 'models/users_model.php';
require 'services/authentication.php';
require 'services/base/token.php';
require 'services/authorization_service.php';
require 'services/rooms_service.php';
require 'models/rooms_model.php';

$router = new Router();


$router->post('/register', function($matches, $query, $body, $headers, $user) {
    try {
        $parse_body = new UserBody($body);
        $authenticate = new Authentication();
        $user_services = new UserService();

        if ($user_services->validate(array(
            'email' => $parse_body->email(),
            'phone' => $parse_body->phone(),
        )) == true) {
            throw new Exception('User Exists');
        }

        $data = $user_services->addUser(array(
            'id' => $parse_body->id(),
            'name' => $parse_body->name(),
            'email' => $parse_body->email(),
            'password' => $parse_body->password(),
            'phone' => $parse_body->phone(),
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});


$router->post('/users', function($matches, $query, $body, $headers, $user) {
    try {
        $parse_body = new UserBody($body);
        $authorization = new Authorization();
        $user_services = new UserService();
        $authorization->authorize($user);

        if ($user_services->validate(array(
            'email' => $parse_body->email(),
            'phone' => $parse_body->phone(),
        )) == true) {
            throw new Exception('User Exists');
        }

        $data = $user_services->addUser(array(
            'id' => $parse_body->id(),
            'name' => $parse_body->name(),
            'email' => $parse_body->email(),
            'password' => $parse_body->password(),
            'phone' => $parse_body->phone(),
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->post('/login', function($matches, $query, $body, $headers, $user) {
    try {
        $authenticate = new Authentication();
        $data = $authenticate->login(array(
            'email' => $body['email'],
            'password' => $body['password'],
        ));

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/logout', function($matches, $query, $body, $headers, $user) {
    try {
        $authenticate = new Authentication();
        $data = $authenticate->logout($user);

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/me', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);

        return array(
            'status' => 'success',
            'data' => $user
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/users', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $user_services = new UserService();
        $authorization->authorize($user);
        $data = $user_services->getUsers();

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/users/{id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $user_services = new UserService();
        $authorization->authorize($user);
        $input['id'] = $matches[0];
        $data = $user_services->getUserById($input);

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->put('/users/{id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $user_services = new UserService();
        $authorization->authorize($user);

        $data = $user_services->editUser(array(
            'id' => $matches[0],
            'name' => $body['name'],
            'email' => $body['email'],
            'phone' => $body['phone'],
        ));

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->delete('/users/{id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $user_services = new UserService();
        $authorization->authorize($user);
        
        $data = $user_services->deleteUser(array(
            'id' => $matches[0],
        ));

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->post('/rooms', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $room_services = new RoomService();
        if (empty($body['to_number'])) {
            throw new Exception('To number is required');
        }

        if (empty($user->phone)) {
            throw new Exception('From number is required');
        }

        if ($body['to_number'] == $user->phone) {
            throw new Exception('To number and from number cannot be the same');
        }

        $new_body = array(
            'to_number' => $body['to_number'],
            'from_number' => $user->phone,
        );

        $parse_body = new RoomBody($new_body);
        $data = $room_services->addRoom(array(
            'id' => $parse_body->id(),
            'numbers' => $parse_body->numbers(),
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/rooms', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $room_services = new RoomService();
        $data = $room_services->getMyRooms($user->phone);

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/rooms/{id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $room_services = new RoomService();
        $data = $room_services->getRoomById($matches[0]);

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->delete('/rooms/{id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $room_services = new RoomService();
        $data = $room_services->deleteRoom($matches[0]);

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->post('/room/{id}/messages', function($matches, $query, $body, $headers, $user) {
    try {
        $data = 'Hello World';

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/room/{id}/messages', function($matches, $query, $body, $headers, $user) {
    try {
        $data = 'Hello World';

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->get('/room/{id}/messages/{message_id}', function($matches, $query, $body, $headers, $user) {
    try {
        $data = 'Hello World';

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->delete('/room/{id}/messages/{message_id}', function($matches, $query, $body, $headers, $user) {
    try {
        $data = 'Hello World';

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$router->put('/room/{id}/messages/{message_id}', function($matches, $query, $body, $headers, $user) {
    try {
        $data = 'Hello World';

        return array(
            'status' => 'success',
            'data' => $data
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'data' => array(
                'message' => $e->getMessage()
            )
        );
    }
});

$response = $router->run();

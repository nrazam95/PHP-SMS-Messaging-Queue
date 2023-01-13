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
require 'services/sms_service.php';
require 'models/sms_model.php';
require 'models/search_by_id_model.php';

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

        $authenticate = new Authentication();
        $token = $authenticate->login(array(
            'email' => $data['email'],
            'password' => $data['password'],
        ));

        return array(
            'status' => 'success',
            'data' => $token
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

$router->get('/me/unread-sms', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $sms_services = new SmsService();
        $data = $sms_services->allMyUnreadSMS(array(
            'user_id' => $user->id,
            'phone' => $user->phone,
        ));
        $total = count($data);
        foreach ($data as $key => $value) {
            $strip = new SMSSearchByIdModel(array(
                'id' => $value->id,
                'room_id' => $value->room_id,
                'user_id' => $value->user_id,
                'message' => $value->message,
                'status' => $value->status,
                'created_at' => $value->created_at,
            ));
            $data[$key] = $strip->toObject();

            if ($value->user_id == $user->id) {
                $data[$key]->from = 'Me';
            } else {
                $data[$key]->from = $strip->getSender($value->user_id)->name;
            }
        }

        return array(
            'status' => 'success',
            'total' => $total,
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

        if (empty($body['message'])) {
            throw new Exception('Message is required');
        }

        if ($body['to_number'] == $user->phone) {
            throw new Exception('To number and from number cannot be the same');
        }

        $new_body = array(
            'to_number' => $body['to_number'],
            'from_number' => $user->phone,
        );

        $parse_body = new RoomBody($new_body);
        $room_created = $room_services->addRoom(array(
            'id' => $parse_body->id(),
            'numbers' => $parse_body->numbers(),
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return array(
            'status' => 'success',
            'data' => $room_created
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

$router->post('/rooms/{id}/sms', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $sms_services = new SmsService();
        $parse_body = new SmsBody(array(
            'room_id' => $matches[0],
            'user_id' => $user->id,
            'message' => $body['message'],
        ));

        if (empty($user->phone)) {
            throw new Exception('From number is required');
        }

        if (empty($body['message'])) {
            throw new Exception('Message is required');
        }

        $data = $sms_services->addSms(array(
            'id' => $parse_body->id(),
            'room_id' => $parse_body->room_id(),
            'user_id' => $parse_body->user_id(),
            'message' => $parse_body->message(),
            'status' => $parse_body->status(),
            'created_at' => $parse_body->created_at(),
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

$router->get('/rooms/{id}/sms', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $smses = new SmsService();
        $data['smses'] = $smses->getSMSByRoomId(array(
            'room_id' => $matches[0],
        ));

        foreach ($data['smses'] as $key => $value) {
            $strip = new SMSSearchByIdModel(array(
                'id' => $value->id,
                'room_id' => $value->room_id,
                'user_id' => $value->user_id,
                'message' => $value->message,
                'status' => $value->status,
                'created_at' => $value->created_at,
            ));
            $data['smses'][$key] = $strip->toObject();

            if ($value->user_id == $user->id) {
                $data['smses'][$key]->from = 'me';
            } else {
                $data['smses'][$key]->from = $strip->getSender($value->user_id)->name;
            }

            // Update status to read
            if ($value->user_id != $user->id) {
                $smses->updateStatusWhenGetSms($value->user_id);
            };
        }

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

$router->get('/rooms/{id}/sms/{sms_id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $smses = new SmsService();
        $data = $smses->getSMSById(array(
            'room_id' => $matches[0],
            'sms_id' => $matches[1],
        ));

        $strip = new SMSSearchByIdModel(array(
            'id' => $data->id,
            'room_id' => $data->room_id,
            'user_id' => $data->user_id,
            'message' => $data->message,
            'status' => $data->status,
            'created_at' => $data->created_at,
        ));

        $data = $strip->toObject();

        if ($data->user_id == $user->id) {
            $data->from = 'me';
        } else {
            $data->from = 'other';
        }

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

$router->delete('/rooms/{id}/sms/{sms_id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $smses = new SmsService();
        $data = $smses->getSMSById(array(
            'room_id' => $matches[0],
            'sms_id' => $matches[1],
        ));

        if ($data == null) {
            throw new Exception('SMS not found');
        }

        if ($data->user_id != $user->id) {
            throw new Exception('You are not allowed to delete this sms');
        }

        $smses->deleteSMSById(array(
            'sms_id' => $matches[1],
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

$router->put('/rooms/{id}/sms/{sms_id}', function($matches, $query, $body, $headers, $user) {
    try {
        $authorization = new Authorization();
        $authorization->authorize($user);
        $smses = new SmsService();
        $data = $smses->getSMSById(array(
            'room_id' => $matches[0],
            'sms_id' => $matches[1],
        ));

        if (empty($body['message'])) {
            throw new Exception('Message is required');
        }

        if ($data == null) {
            throw new Exception('SMS not found');
        }

        if ($data->user_id != $user->id) {
            throw new Exception('You are not allowed to edit this sms');
        }

        $data = $smses->updateSMS(array(
            'id' => $data->id,
            'room_id' => $data->room_id,
            'user_id' => $user->id,
            'message' => $body['message'],
            'status' => $data->status,
            'created_at' => $data->created_at,
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

$response = $router->run();
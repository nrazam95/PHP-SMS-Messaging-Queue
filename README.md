
# PHP SMS Messages Queueing 

```
#Question

Brief:

Write a simple PHP Application that offers queueing of SMS Messages.

 

Requirements:

Application must be written in PHP (8.x preferably)
HTTP API to insert an SMS Message in the queue
HTTP API to consume an SMS Message from the queue and returns it in JSON format (FIFO)
HTTP API to get the total number of messages in the queue 
HTTP API to get all SMS messages in the queue in JSON format
Have everything running without any dependency on an external system (database, service, etc.)
Simple readme on how to use the application
 

Bonus but Not Compulsory:

Use of composer packages where convenient instead of building from scratch
Secure coding with correct amount of filters applied
Have the project working inside a Docker container (that builds and runs locally)
Use vanilla PHP (no framework)
Submit the exercise via a github link
 

What we are looking for:

Adherence to standard coding style (PSR-12)
Adherence to modern coding standards (OOP, SOLID, DRY)
Code that can be easily understood with just right amount of clear and concise comments
Use of proper API methods for each endpoint (GET, POST, etc)

```

## There are 2 Applications in this repository

|         | Description | Run on Local | Run on Docker |
|---------|-------------|--------------|---------------|
| Full-Stack Socket.io | This is a full-stack application that uses Socket.io to send SMS messages to the client. |```php start.php start```|```docker compose up -d```|
| API | This is a simple API application that uses a queue to send SMS messages to the client. |```php server.php```|```docker compose up -d```|


## Installation & Run on Local (Full-Stack Socket.io)

1. Clone the repository

```bash
git clone https://github.com/nrazam95/PHP-SMS-Messaging-Queue.git
```

2. Install dependencies

```bash
composer install
```

3. Run the application

```bash
php start.php start
```


## Installation & Run on Local (API)

1. Clone the repository

```bash
git clone https://github.com/nrazam95/PHP-SMS-Messaging-Queue.git
```

2. Install dependencies

```bash
composer install
```

3. Run the application

```bash
php -S localhost:8000 server.php
```

## Installation & Run on Docker (Full-Stack Socket.io && API)

1. Clone the repository

```bash
git clone https://github.com/nrazam95/PHP-SMS-Messaging-Queue.git
```

2. Run the application

```bash
docker compose up -d
```

## Prepare Your Environment

1. Create a .env file in the root directory of the project

```bash
cp .env.example .env
```


## Usage

1. Open your browser and go to http://{YOUR_HOST}:{YOUR_PORT}/



## Architecture

### Full-Stack Socket.io

1. The client connects to the server using Socket.io
2. The client sends a request to the server to send an SMS message
3. The server sends the SMS message to the client using Socket.io
4. The client receives the SMS message from the server using Socket.io
5. The client sends a request to the server to get all SMS messages
6. The server sends all SMS messages to the client using Socket.io
7. The client receives all SMS messages from the server using Socket.io

```mermaid
graph LR
A[Client] --> B[Server]
B --> C[Socket.io]
C --> D[Client]
D --> E[Server]
E --> F[Socket.io]
F --> G[Client]
```

```mermaid
sequenceDiagram
Client->>Server: Send SMS Message
Server->>Client: SMS Message
Client->>Server: Get All SMS Messages
Server->>Client: All SMS Messages
```

### API

1. The client sends a request to the server to send an SMS message
2. The server sends the SMS message to the client
3. The client sends a request to the server to get all SMS messages
4. The server sends all SMS messages to the client

```mermaid
graph LR
A[Client] --> B[Server]
B --> C[Client]
C --> D[Server]
D --> E[Client]
```

```mermaid
sequenceDiagram
Alice->>Server: Send SMS Message
Server-->>Bob: Receive SMS Message (Unread)
Bob->>Server: Request SMS Message
Server->>Bob: Read SMS Message (Read)
Bob->>Server: Send SMS Message
Server-->>Alice: Receive SMS Message (Unread)
Alice->>Server: Request SMS Message
Server->>Alice: Read SMS Message (Read)
Alice->>Server: Request All SMS Messages
```

# PHP SMS Messages Queueing Documentation

| Category | Name | Description | Method | Endpoint | 
|----------|------|-------------|--------|----------|
| Authentication | Register | Register a new user | POST | /signup |
| Authentication | Login | Login a user | POST | /login |
| Authentication | Logout | Logout a user | POST | /logout |
| User | Delete User | Delete the authenticated user | DELETE | /users |
| User | Get All Users | Get all users | GET | /users|
| User | Get User By ID | Get a user by ID | GET | /users/{id} |
| User | Update User By ID | Update a user by ID | PUT | /users/{id} |
| User | Delete User By ID | Delete a user by ID | DELETE | /users/{id} |
| Room | Create Room | Create a new room | POST | /rooms |
| Room | Get All Rooms | Get all rooms | GET | /rooms |
| Room | Get Room By ID | Get a room by ID | GET | /rooms/{id} |
| Room | Update Room By ID | Update a room by ID | PUT | /rooms/{id} |
| Room | Delete Room By ID | Delete a room by ID | DELETE | /rooms/{id} |
| SMS | Send SMS Message | Send an SMS message | POST | /rooms/{id}/ |
| SMS | Get All SMS Messages | Get all SMS messages | GET | /rooms/{id}/ |
| SMS | Get SMS Message By ID | Get an SMS message by ID | GET | /rooms/{id}/sms/{sms_id} |
| SMS | Update SMS Message By ID | Update an SMS message by ID | PUT | /rooms/{id}/sms/{sms_id} |
| SMS | Delete SMS Message By ID | Delete an SMS message by ID | DELETE | /rooms/{id}/sms/{sms_id} |
| MySelf | Get Myself | Get the authenticated user | GET | /me |
| MySelf | All Unread SMS Messages | Get all unread SMS messages | GET | /me/unread-sms |

## Authentication

<table>
    <tr>
        <td valign="top" width="50%">
            <h1>Register</h1>
            <p>Register a new user</p>
            <h4>Endpoint</h4>
            <p><code>/signup</code></p>
            <h4>Method</h4>
            <p><code>POST</code></p>
            <h4>Parameters</h4>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>name</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The name of the user</td>
                </tr>
                <tr>
                    <td>email</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The email of the user</td>
                </tr>
                <tr>
                    <td>phone</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The phone number of the user</td>
                </tr>
                <tr>
                    <td>password</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The password of the user</td>
                </tr>
            </table>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {
                        "name": "John Doe",
                        "email": "xxx@xxx.com",
                        "phone": "0123456789",
                        "password": "123456",
                    }
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": {
                            "name": "John Doe",
                            "email": "xxx",
                            "created_at": "2021-08-01T12:00:00.000000Z",
                        }
                    }
            </code></pre>
        </td>
        <td valign="top" width="50%">
            <h1>Login</h1>
            <p>Login a user</p>
            <h4>Endpoint</h4>
            <p><code>/login</code></p>
            <h4>Method</h4>
            <p><code>POST</code></p>
            <h4>Parameters</h4>
            <table>
                <tr>
                    <td><code>email</code></td>
                    <td><code>string</code></td>
                    <td><code>required</code></td>
                </tr>
                <tr>
                    <td><code>password</code></td>
                    <td><code>string</code></td>
                    <td><code>required</code></td>
                </tr>
            </table>
            <h4>Notes</h4>
            <p>On success, the response will contain a JWT token that will be used for authentication.</p>
            <p>On failure, the response will contain an error message.</p>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {
                        "email": "xxx@xxx.com",
                        "password": "123456",
                    }
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": "ajhebwebuywuyeqbquywbBEJBQJBiubsdbhfwe="
                    }
            </code></pre>
        </td>
        <td valign="top" width="50%">
            <h1>Logout</h1>
            <p>Logout a user</p>
            <h4>Endpoint</h4>
            <p><code>/logout</code></p>
            <h4>Method</h4>
            <p><code>POST</code></p>
            <h4>Parameters</h4>
            <h4>Notes</h4>
            <p>No parameters are required.</p>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {}
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": "User logged out successfully",
                    }
            </code></pre>
        </td>
    </tr>
</table>

## User

<table>
    <tr>
        <td valign="top" width="50%">
            <h1>Create User</h1>
            <p>Create a new user</p>
            <h4>Endpoint</h4>
            <p><code>/users</code></p>
            <h4>Method</h4>
            <p><code>POST</code></p>
            <h4>Parameters</h4>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>name</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The name of the user</td>
                </tr>
                <tr>
                    <td>email</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The email of the user</td>
                </tr>
                <tr>
                    <td>phone</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The phone number of the user</td>
                </tr>
                <tr>
                    <td>password</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The password of the user</td>
                </tr>
            </table>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {
                        "name": "John Doe",
                        "email": "xxx@xxx.com",
                        "phone": "0123456789",
                        }
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": {
                            "name": "John Doe",
                            "email": "xxx",
                            "created_at": "2021-08-01T12:00:00.000000Z",
                        }
                    }
            </code></pre>
        </td>
        <td valign="top" width="50%">
            <h1>Get User</h1>
            <p>Get a user</p>
            <h4>Endpoint</h4>
            <p><code>/users/{id}</code></p>
            <h4>Method</h4>
            <p><code>GET</code></p>
            <h4>Parameters</h4>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>id</td>
                    <td>integer</td>
                    <td>yes</td>
                    <td>The id of the user</td>
                </tr>
            </table>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {}
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": {
                            "name": "John Doe",
                            "email": "xxx",
                            "created_at": "2021-08-01T12:00:00.000000Z",
                        }
                    }
            </code></pre>
        </td>
        <td valign="top" width="50%">
            <h1>Get Users</h1>
            <p>Get all users</p>
            <h4>Endpoint</h4>
            <p><code>/users</code></p>
            <h4>Method</h4>
            <p><code>GET</code></p>
            <h4>Parameters</h4>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {}
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": [
                            {
                                "name": "John Doe",
                                "email": "xxx",
                                "created_at": "2021-08-01T12:00:00.000000Z",
                            },
                            {
                                "name": "Jane Doe",
                                "email": "xxx",
                                "created_at": "2021-08-01T12:00:00.000000Z",
                            }
                        ]
                    }
            </code></pre>
        </td>
        <td valign="top" width="50%">
            <h1>Update User</h1>
            <p>Update a user</p>
            <h4>Endpoint</h4>
            <p><code>/users/{id}</code></p>
            <h4>Method</h4>
            <p><code>PUT</code></p>
            <h4>Parameters</h4>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>id</td>
                    <td>integer</td>
                    <td>yes</td>
                    <td>The id of the user</td>
                </tr>
            </table>
            <h4>Body Type</h4>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>name</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The name of the user</td>
                </tr>
                <tr>
                    <td>email</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The email of the user</td>
                </tr>
                <tr>
                    <td>phone</td>
                    <td>string</td>
                    <td>yes</td>
                    <td>The phone number of the user</td>
                </tr>
            </table>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {
                        "name": "John Doe",
                        "email": "xxx",
                        "phone": "0123456789",
                    }
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": {
                            "name": "John Doe",
                            "email": "xxx",
                            "created_at": "2021-08-01T12:00:00.000000Z",
                        }
                    }
            </code></pre>
        </td>
        <td valign="top" width="50%">
            <h1>Delete User</h1>
            <p>Delete a user</p>
            <h4>Endpoint</h4>
            <p><code>/users/{id}</code></p>
            <h4>Method</h4>
            <p><code>DELETE</code></p>
            <h4>Parameters</h4>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>id</td>
                    <td>integer</td>
                    <td>yes</td>
                    <td>The id of the user</td>
                </tr>
            </table>
            <h4>Example Request</h4>
            <pre><code class="language-javascript">
                    {}
            </code></pre>
            <h4>Example Response</h4>
            <pre><code class="language-javascript">
                    {
                        "status": "success",
                        "data": "User deleted successfully",
                    }
            </code></pre>
        </td>
    </tr>
</table>















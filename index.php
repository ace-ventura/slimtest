<?php
require 'vendor/autoload.php';
require 'config.inc.php';

require 'src/DataManager.php';
require 'db/pdoConnection.php';

use SlimTest\DM\PDO_Connection;
PDO_Connection::setSettings($config['db']);

require 'implemented/dataStructureDef.php';
require 'implemented/restfulEntityDef.php';

require 'src/Auth.php';

use SlimTest\Entity\RestfulEntityManager;
use SlimTest\Auth\Auth;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

$app = new Slim\App(['settings' => $config]);
$container = $app->getContainer();
$delim = (strpos(php_uname(), 'Windows') === false ? '/' : '\\');
$container['upload_directory'] = __DIR__ . $delim .'uploaded'.$delim;


/**
 * Route definition
 */

// Index 
$app->get('/', function ($request, $response, $args) {
    $newResponse = $response->withJSON(['status' => 'OK', 'greeting' => 'Congrats! It works!']);
    return $newResponse;
});

// Authentification
$app->get('/auth/login', function ($request, $response, $args) {
    $auth = Auth::getInstance();
    return $auth->login($request, $response, $args);
});


// Check session before any other action
$app->add(function ($request, $response, $next) {
    $headers = $request->getHeaders();
    $token = $headers['HTTP_X_AUTH_TOKEN'][0];
    if ($token) {
        $auth = Auth::getInstance();
        $sessionData = $auth->getSessionData($token);
    } else {
        $sessionData = [];
    }
    $request = $request->withAttribute('sessionData', $sessionData);
    return $next($request, $response);
});

// Restful management

$app->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/{name}[/{id:[0-9]+}]', function ($request, $response, $args) {
    $name = $args['name'];

    $restfulEntity = RestfulEntityManager::getByName($name);
    if ($restfulEntity === false) {
        return $response->withJSON(['status' => 'fail', 'message' => 'Route not served']);
    }

    $sessionData = $request->getAttribute('sessionData');
    if (!$sessionData) {
        $newResponse = $response->withJSON(['status' => 'fail', 'message' => 'You are not authorized to perform this request']);

    } else {
        $auth = Auth::getInstance();
        $allowed = $auth->checkEntityPermission($sessionData['permLevel'], $restfulEntity->getRequiredPermission());

        if (!$allowed) {
            $newResponse = $response->withJSON(['status' => 'fail', 'message' => 'You are not allowed to manage '.$name]);

        } else {
            $method = $request->getMethod();
            $id = $args['id'];

            if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                $input = $request->getParsedBody();

                $directory = $this->get('upload_directory');
                $uploadedFiles = $request->getUploadedFiles();
                if ($uploadedFiles) {

                    $fileField = $restfulEntity->getDataDef()->getFileField();
                    $uploadedFile = $uploadedFiles[$fileField];
                    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                        $filename = $uploadedFile->getClientFilename();
                        $uploadedFile->moveTo($directory.$filename);
                        $input[$fileField] = $filename;
                    }                
                }
            }

            if ($method == 'GET') {
                if ($id) {
                    $answer = $restfulEntity->getByID($id);
                } else {
                    $answer = $restfulEntity->getList();
                }

            } elseif ($method == 'POST') {
                if ($id) {
                    $answer = ['status' => 'fail', 'message' => 'For update specified item methods PUT or PATCH should be used'];
                } else {
                    if (empty($input)) {
                        $answer = ['status' => 'fail', 'message' => 'Input is empty'];
                    } else {
                        $answer = $restfulEntity->add($input);
                    }
                }

            } elseif ($method == 'DELETE') {
                if ($id) {
                    $answer = $restfulEntity->delete($id);
                } else {
                    $answer = ['status' => 'fail', 'message' => 'Record id must be specified'];
                }

            } elseif ($method == 'PUT' || $method == 'PATCH') {
                if ($id) {
                    if (empty($input)) {
                        $answer = ['status' => 'fail', 'message' => 'Input is empty'];
                    } else {    
                        $answer = $restfulEntity->update($input, $id);
                    }
                } else {
                    $answer = ['status' => 'fail', 'message' => 'Record id must be specified'];
                }

            }

            $newResponse = $response->withJSON($answer);

            //
        }
    }
    // $restfulEntity->getList
    return $newResponse;
});



$app->run();


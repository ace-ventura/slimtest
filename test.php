<?php
declare(strict_types=1);
require 'vendor/autoload.php';
use \Curl\Curl;
use PHPUnit\Framework\TestCase;

/**
 * Change this before test exec
 */
function main_uri() {
    return 'http://api.arkady.ru';
};

$delim = (strpos(php_uname(), 'Windows') === false ? '/' : '\\');
function get_delim() {
    return (strpos(php_uname(), 'Windows') === false ? '/' : '\\');
} 


class TestResultStorage
{
    public static $token;
}

final class AllTest extends TestCase
{
    public function testLoginAsAdminWithDefaultCredentials(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Login', 'admin');
        $curl->setHeader('X-Auth-Password', '12345');

        $curl->get(main_uri().'/auth/login');
        //print_r($curl->response);

        $response = (array)$curl->response;

        $this->assertEquals($response['status'], 'OK');
    }

    public function testTokenReceived(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Login', 'admin');
        $curl->setHeader('X-Auth-Password', '12345');

        $curl->get(main_uri().'/auth/login');
        //print_r($curl->response);
        TestResultStorage::$token = $curl->responseHeaders['x-auth-token'];

        $this->assertArrayHasKey('x-auth-token', $curl->responseHeaders);
    }

    public function testAuthWithTokenAndGetUserList(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);

        $curl->get(main_uri().'/users');
        $data = $curl->response;
        $data = json_decode(json_encode($data), true);
        foreach($data as $item) {
            if ($item['user_id'] == 1) {
                $login = trim($item['login']);
            }
        }

        $this->assertEquals($login, 'admin');
    }

    public function testAddNewUser(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);

        $curl->post(main_uri().'/users', ['login' => 'new_user', 'pwd' => 'qwerty']);

        $response = (array)$curl->response;
        $this->assertEquals($response['status'], 'OK');
    }


    public function testNewUserIsAdded(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);

        $curl->get(main_uri().'/users');
        $data = $curl->response;
        $data = json_decode(json_encode($data), true);
        $uid = 0;
        foreach($data as $item) {
            if (trim($item['login']) == 'new_user') {
                $uid = intval($item['user_id']);
            }
        }

        $this->assertGreaterThan(0, $uid);
    }

    public function testChangeNewUserFullName(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);

        $curl->get(main_uri().'/users');
        $data = $curl->response;
        $data = json_decode(json_encode($data), true);
        $uid = 0;
        foreach($data as $item) {
            if (trim($item['login']) == 'new_user') {
                $uid = intval($item['user_id']);
                break;
            }
        }

        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);
        $curl->put(main_uri().'/users/'.$uid, ['full_name' => 'New User']);

        $response = (array)$curl->response;
        $this->assertEquals($response['status'], 'OK');
    }


    public function testLoginAsNewUser(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Login', 'new_user');
        $curl->setHeader('X-Auth-Password', 'qwerty');

        $curl->get(main_uri().'/auth/login');
        //print_r($curl->response);

        $response = (array)$curl->response;
        TestResultStorage::$token = $curl->responseHeaders['x-auth-token'];

        $this->assertEquals($response['status'], 'OK');
    }

    public function testAddRecipe(): void
    {
        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);

        $curl->post(main_uri().'/recipes', [
            'title' => 'Пироги',
            'picture' => new CURLFile(__DIR__ .get_delim().'pic.jpg'),
        ]);
        
        $curl = new Curl();
        $curl->setHeader('X-Auth-Token', TestResultStorage::$token);
        $curl->get(main_uri().'/recipes');
        $data = $curl->response;
        $data = json_decode(json_encode($data), true);
        $recipe_id = 0;
        foreach($data as $item) {
            if (trim($item['title']) == 'Пироги') {
                $recipe_id = intval($item['recipe_id']);
                break;
            }
        }
        $this->assertGreaterThan(0, $recipe_id);
    }


}





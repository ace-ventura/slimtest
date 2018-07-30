<?php
namespace SlimTest\Auth;
//require_once 'Permission.php';

use SlimTest\DM\PdoDataManager;
use SlimTest\DSD\EntityDSManager;
use SlimTest\Permission\Permission;

/**
 * Authentification, session management, checking permission
 * Singleton
 */
class Auth 
{
    private $userDataManager;
    private $sessionDataManager;

    private $sessionData;

    private static $instance;

    private function __construct()
    {
        $this->userDataManager     = new PdoDataManager('public."user"', EntityDSManager::getByName('user'));
        $this->sessionDataManager  = new PdoDataManager('public."session"', EntityDSManager::getByName('session'));
    }

    public function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;        
    }

    public function login($request, $response, $args) 
    {
        $headers = $request->getHeaders();

        $login = $headers['HTTP_X_AUTH_LOGIN'][0];
        $pwd = $headers['HTTP_X_AUTH_PASSWORD'][0];

        $newResponse = $response;

        if (empty($login) || empty($pwd)) {
            $answer = ['status' => 'fail', 'message' => 'Login or password is empty'];
        } else {
            $userData = $this->userDataManager->getRow(['login' => $login, 'pwd' => md5($pwd)]);
            if (empty($userData)) {
                $answer = ['status' => 'fail', 'message' => 'User with specified credentials not found'];
            } else {
                $session_id = $this->startNewSession($userData['user_id']);
                $newResponse = $newResponse->withHeader('X-Auth-Token', $session_id);
                $answer = ['status' => 'OK', 'message' => 'Successfully logged in'];
            }
        }
        return $newResponse->withJSON($answer);
    }

    private function startNewSession(int $uid): string
    {
        $row = $this->sessionDataManager->getRow(['user_id' => $uid]);
        if (!empty($row)) {
            $this->sessionDataManager->delete($row['session_id']);
        }
        $session_id = md5(rand(11111, 99999));
        $this->sessionDataManager->add(['session_id' => $session_id, 'user_id' => $uid]);
        return $session_id;
    }

    public function getSessionData(string $token): array
    {
        if ($this->sessionData) {
            return $this->sessionData;
        } else {
            $row = $this->sessionDataManager->getRow(['session_id' => $token]);
            if ($row) {
                $this->sessionData = [
                    'user_id' => $row['user_id'], 
                    'permLevel' => $this->getUserPermission($row['user_id'])
                ];
            } else {
                $this->sessionData = [];
            }
            return $this->sessionData;
        }
    }

    public function getLoggedUID()
    {
        return $this->sessionData['user_id'];
    }

    private function getUserPermission(int $uid): int
    {
        if ($uid == 1) {
            return Permission::PERM_LEVEL_ADMIN;
        } else {
            return Permission::PERM_LEVEL_NORMAL;
        }
    }

    public function checkEntityPermission($userPermissionLevel, $entityPermissionRequired)
    {
        if ($entityPermissionRequired & Permission::PERM_ALLOWED_FOR_ALL) {
            return true;
        } elseif ($entityPermissionRequired & Permission::PERM_ALLOWED_FOR_NORMAL) {
            if ($userPermissionLevel == Permission::PERM_LEVEL_NORMAL) {
                return true;
            } else {
                return false;
            }
        } elseif ($entityPermissionRequired & Permission::PERM_ALLOWED_FOR_ADMIN) {
            if ($userPermissionLevel == Permission::PERM_LEVEL_ADMIN) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new \Exception('Unknown permission level: '.$entityPermissionRequired);
        }
    }
}

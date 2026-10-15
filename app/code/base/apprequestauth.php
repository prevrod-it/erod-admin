<?php
class AppRequestAuth
{
    public static function is_legacy($data)
    {
        return is_array($data) && (!array_key_exists('appversion', $data)
            || (is_string($data['appversion']) && trim($data['appversion']) === ''));
    }

    public static function authorize($data, $key, $endpoint, $check_session)
    {
        if (!is_array($data) || !is_string($key) || $key === ''
            || !isset($data['reqcode']) || !is_string($data['reqcode'])
            || !hash_equals($key, $data['reqcode'])) {
            return false;
        }
        if (self::is_legacy($data)) {
            return true;
        }
        // Any supplied version opts in; malformed values must not downgrade to legacy.
        if (!is_string($data['appversion']) && !is_int($data['appversion'])
            && !is_float($data['appversion'])) {
            return false;
        }
        if ($endpoint === 'auth' && isset($data['qaction']) && $data['qaction'] === 'applogin') {
            return true; // Credentials are checked by the login handler.
        }
        if ($endpoint === 'auth' && (!isset($data['qaction'])
            || !in_array($data['qaction'], array('appsescheck', 'applogout'), true))) {
            return false;
        }
        $field = $endpoint === 'auth' ? 'appuser' : 'userid';
        if (!isset($data[$field]) || (!is_int($data[$field]) && !is_string($data[$field]))) {
            return false;
        }
        $user = filter_var($data[$field], FILTER_VALIDATE_INT,
            array('options' => array('min_range' => 1, 'max_range' => 2147483647)));
        if ($user === false || !isset($data['appses']) || !is_string($data['appses'])
            || !preg_match('/\A[a-f0-9]{32}\z/', $data['appses'])) {
            return false;
        }
        return $check_session($user, $data['appses']) === true;
    }

    public static function deny($data, $endpoint)
    {
        if (self::is_legacy($data)) {
            echo 'ERROR - Unathorized Access!';
            return;
        }
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        $response = array('authstate' => false, 'autherror' => 'Invalid API key or session.');
        if ($endpoint === 'auth') {
            $response = array('appuser' => 0, 'appsession' => null, 'ttl' => 0) + $response;
        }
        echo json_encode($response);
    }
}

<?php
// Standalone tests with synthetic values and doubles; no database or API calls.
require_once __DIR__ . '/../app/code/base/apprequestauth.php';
require_once __DIR__ . '/../app/code/base/core.php';

$checks = 0;
function verify($condition, $message)
{
    global $checks;
    $checks++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$key = 'synthetic-test-key';
$token = str_repeat('a', 32);
$calls = 0;
$lookup = function ($user, $session) use (&$calls, $token) {
    $calls++;
    return $user === 7 && $session === $token;
};
$valid = array('reqcode' => $key, 'appversion' => '2.0.0', 'userid' => 7, 'appses' => $token);
foreach (array(array('reqcode' => $key), array('reqcode' => $key, 'appversion' => ''),
    array('reqcode' => $key, 'appversion' => '  ')) as $legacy) {
    verify(AppRequestAuth::authorize($legacy, $key, 'data', $lookup), 'Legacy request rejected');
}
verify($calls === 0, 'Legacy requests queried sessions');
verify(AppRequestAuth::authorize($valid, $key, 'data', $lookup), 'Valid session rejected');
verify($calls === 1, 'Data request did not use exactly one lookup');
foreach (array('reqcode' => 'wrong', 'appversion' => array(), 'userid' => '7 OR 1=1',
    'appses' => array()) as $field => $value) {
    $bad = $valid;
    $bad[$field] = $value;
    $before = $calls;
    verify(!AppRequestAuth::authorize($bad, $key, 'data', $lookup), 'Malformed input accepted: ' . $field);
    verify($calls === $before, 'Malformed input reached database');
}
foreach (array(null, false, array()) as $version) {
    $bad = $valid;
    $bad['appversion'] = $version;
    verify(!AppRequestAuth::authorize($bad, $key, 'data', $lookup), 'Malformed version downgraded to legacy');
}
foreach (array('0', 0, 2, '2.1') as $version) {
    $request = $valid;
    $request['appversion'] = $version;
    $before = $calls;
    verify(AppRequestAuth::authorize($request, $key, 'data', $lookup), 'Version rejected');
    verify($calls === $before + 1, 'Nonempty version bypassed session validation');
}
foreach (array('userid', 'appses') as $field) {
    $bad = $valid;
    unset($bad[$field]);
    verify(!AppRequestAuth::authorize($bad, $key, 'data', $lookup), 'Missing identity accepted');
}
$bad = $valid;
$bad['userid'] = 8;
verify(!AppRequestAuth::authorize($bad, $key, 'data', $lookup), 'Other user accepted');
$bad = $valid;
$bad['appses'] = str_repeat('b', 32);
verify(!AppRequestAuth::authorize($bad, $key, 'data', $lookup), 'Other token accepted');
verify(!AppRequestAuth::authorize($valid, $key, 'data', function () { return false; }), 'Expired/revoked lookup result ignored');
verify(!AppRequestAuth::authorize($valid, '', 'data', $lookup), 'Empty API configuration accepted');
verify(!AppRequestAuth::authorize(null, $key, 'data', $lookup), 'Malformed JSON accepted');
$before = $calls;
verify(AppRequestAuth::authorize(array('reqcode' => $key, 'appversion' => '2', 'qaction' => 'applogin'),
    $key, 'auth', $lookup), 'Login incorrectly requires a session');
verify($calls === $before, 'Login performed session lookup');
verify(!AppRequestAuth::authorize($valid + array('qaction' => 'applogin'), $key, 'data',
    function () { return false; }), 'Data endpoint gained login bypass');
foreach (array('appsescheck', 'applogout') as $action) {
    $auth = $valid + array('appuser' => 7, 'qaction' => $action);
    unset($auth['userid']);
    verify(AppRequestAuth::authorize($auth, $key, 'auth', $lookup), 'Auth action rejected');
    unset($auth['appuser']);
    verify(!AppRequestAuth::authorize($auth, $key, 'auth', $lookup), 'Auth action accepted without appuser');
}

class SessionStatementDouble
{
    public $num_rows = 1;
    public $closed = false;
    public $execute_ok = true;
    public function bind_param($types, &$user, &$session) {
        verify($types === 'is' && $user === 7 && strlen($session) === 32, 'Incorrect parameter binding');
        return true;
    }
    public function execute() { return $this->execute_ok; }
    public function store_result() { return true; }
    public function close() { $this->closed = true; }
}
class SessionConnectionDouble
{
    public $statement;
    public function prepare($sql) {
        verify(strpos($sql, 'SELECT 1 FROM publicsessions') === 0, 'Lookup is not read-only');
        verify(strpos($sql, 'iSESuserid = ? AND sSEShash = ?') !== false, 'Missing ownership/token predicate');
        verify(strpos($sql, 'iSESexp > 0') !== false && strpos($sql, '> UNIX_TIMESTAMP() LIMIT 1') !== false,
            'Missing expiry/revocation predicate');
        return $this->statement;
    }
}
$reflection = new ReflectionClass('Core');
$instance = $reflection->getProperty('instance');
$instance->setAccessible(true);
$instance->setValue(null, $reflection->newInstanceWithoutConstructor());
$db = new SessionConnectionDouble;
Core::$mysqli = $db;
foreach (array(array(1, true, true), array(0, true, false), array(1, false, false)) as $case) {
    $db->statement = new SessionStatementDouble;
    $db->statement->num_rows = $case[0];
    $db->statement->execute_ok = $case[1];
    verify(Core::has_public_session(7, $token) === $case[2], 'Unexpected session lookup result');
    verify($db->statement->closed, 'Statement was not closed');
}
$instance->setValue(null, null);
Core::$mysqli = null;

ob_start();
AppRequestAuth::deny(array('reqcode' => 'wrong'), 'data');
verify(ob_get_clean() === 'ERROR - Unathorized Access!', 'Legacy error changed');
ob_start();
AppRequestAuth::deny($valid, 'auth');
$error = json_decode(ob_get_clean(), true);
verify(http_response_code() === 401 && $error['authstate'] === false && array_key_exists('appsession', $error),
    'Versioned error contract incorrect');
echo "Passed $checks checks; no database connections or application requests.\n";

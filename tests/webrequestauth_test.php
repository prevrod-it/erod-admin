<?php
// Synthetic session identities only. No application requests or service connections.
require_once __DIR__ . '/../app/code/base/webrequestauth.php';
$checks = 0;
function check_web($value, $message) {
    global $checks;
    $checks++;
    if (!$value) { throw new RuntimeException($message); }
}
$key = 'synthetic-web-test-key';
$token = str_repeat('a', 32);
$calls = 0;
$lookup = function ($role, $user, $session) use (&$calls, $token) {
    $calls++;
    if ($session !== $token || !in_array($user, array(7, 8), true)) { return false; }
    return array('user' => $user, 'company' => 20, 'type' => $user === 7 ? 1 : 2);
};
$admin = array('reqcode' => $key, 'csrf' => WebRequestAuth::csrf('admin', 7, $token));
$client = array('reqcode' => $key, 'csrf' => WebRequestAuth::csrf('public', 7, $token));
$driver = array('reqcode' => $key, 'csrf' => WebRequestAuth::csrf('public', 8, $token), 'userid' => 8);
$adminCookies = array('adminactivesession' => '7|' . $token);
$clientCookies = array('publicactivesession' => '7|' . $token);
$driverCookies = array('publicactivesession' => '8|' . $token);
$endpoints = array('adminentity.php', 'adminopunit.php', 'adminplans.php', 'adminpubusers.php', 'adminvehicles.php',
    'pubadminentity.php', 'pubadminopunit.php', 'pubadminpubusers.php', 'pubadminvehicles.php', 'usersapi.php', 'reportapi.php');
foreach ($endpoints as $endpoint) {
    $isAdmin = WebRequestAuth::role($endpoint) === 'admin';
    $isDriver = in_array($endpoint, array('usersapi.php', 'reportapi.php'), true);
    $post = $isAdmin ? $admin : ($isDriver ? $driver : $client);
    $cookies = $isAdmin ? $adminCookies : ($isDriver ? $driverCookies : $clientCookies);
    $before = $calls;
    check_web(is_array(WebRequestAuth::authenticate($post, $cookies, $endpoint, $key, $lookup)), 'Valid request rejected: ' . $endpoint);
    check_web($calls === $before + 1, 'Expected one session lookup');
    foreach (array('reqcode', 'csrf') as $field) {
        $bad = $post; $bad[$field] = 'invalid'; $before = $calls;
        check_web(!WebRequestAuth::authenticate($bad, $cookies, $endpoint, $key, $lookup), 'Invalid field accepted');
        check_web($calls === $before, 'Invalid field reached database');
    }
    check_web(!WebRequestAuth::authenticate($post, array(), $endpoint, $key, $lookup), 'Missing cookie accepted');
    check_web(!WebRequestAuth::authenticate($post, $cookies, $endpoint, $key, function () { return false; }), 'Expired/revoked session result ignored');
    $source = file_get_contents(__DIR__ . '/../app/code/processing/' . $endpoint);
    check_web(strpos($source, 'WebRequestAuth::require_request(basename(__FILE__))') !== false, 'Missing endpoint integration');
}
check_web(!WebRequestAuth::authenticate($admin, $clientCookies, 'adminentity.php', $key, $lookup), 'Public cookie accepted as admin');
check_web(!WebRequestAuth::authenticate($driver, $driverCookies, 'pubadminvehicles.php', $key, $lookup), 'Driver gained company admin access');
$bad = $driver; $bad['userid'] = 99;
check_web(!WebRequestAuth::authenticate($bad, $driverCookies, 'usersapi.php', $key, $lookup), 'Driver impersonated another user');
$bad = $admin; $bad['csrf'] = WebRequestAuth::csrf('public', 7, $token);
check_web(!WebRequestAuth::authenticate($bad, $adminCookies, 'adminentity.php', $key, $lookup), 'Cross-role CSRF accepted');
foreach (array('7|invalid', '0|' . $token, '7 OR 1=1|' . $token, array('bad'), '7|' . $token . '|extra') as $cookie) {
    $before = $calls;
    check_web(!WebRequestAuth::authenticate($admin, array('adminactivesession' => $cookie), 'adminentity.php', $key, $lookup), 'Malformed cookie accepted');
    check_web($calls === $before, 'Malformed cookie reached database');
}
check_web(!WebRequestAuth::authenticate($admin, $adminCookies, 'unknown.php', $key, $lookup), 'Unknown endpoint accepted');
check_web(!WebRequestAuth::authenticate($admin, $adminCookies, 'adminentity.php', '', $lookup), 'Missing configured key accepted');
check_web(WebRequestAuth::csrf('admin', 0, '') === '', 'Logged-out page has CSRF token');
check_web(WebRequestAuth::csrf('admin', 7, str_repeat('b', 32)) !== $admin['csrf'], 'CSRF is not session-bound');

$identity = array('user' => 7, 'role' => 'public', 'company' => 20, 'type' => 1);
$owns = function ($kind, $id, $company) { return $company === 20 && in_array($id, array(20, 30), true); };
foreach (array('pubadminopunit.php' => 'entityid', 'pubadminvehicles.php' => 'entity', 'pubadminpubusers.php' => 'entityid') as $endpoint => $field) {
    foreach (array('insert', 'update', 'delete') as $action) {
        $post = array('formaction' => $action, $field => 20, 'itemid' => 30, 'opunit' => 0);
        check_web(WebRequestAuth::scope($identity, $endpoint, $post, $owns), 'Own-company operation denied');
        $bad = $post; $bad[$field] = 99;
        check_web(!WebRequestAuth::scope($identity, $endpoint, $bad, $owns), 'Foreign destination company allowed');
        if ($action !== 'insert') {
            $bad = $post; $bad['itemid'] = 99;
            check_web(!WebRequestAuth::scope($identity, $endpoint, $bad, $owns), 'Foreign item allowed');
        }
    }
}
check_web(!WebRequestAuth::scope($identity, 'pubadminentity.php', array('formaction' => 'insert'), $owns), 'Client could create a company');
check_web(WebRequestAuth::scope($identity, 'pubadminentity.php', array('formaction' => 'update', 'itemid' => 20), $owns), 'Own company denied');
check_web(!WebRequestAuth::scope($identity, 'pubadminentity.php', array('formaction' => 'update', 'itemid' => 20, 'planid' => 99), $owns), 'Foreign plan allowed');
check_web(!WebRequestAuth::scope($identity, 'pubadminpubusers.php', array('formaction' => 'insert', 'entityid' => 20, 'opunit' => 99), $owns), 'Foreign operating unit allowed');
$driverIdentity = array('user' => 8, 'company' => 20, 'role' => 'public', 'type' => 2);
$belongs = function ($kind, $id) { return $kind === 'control' ? $id === 'synthetic-control' : $id === 30; };
$activity = array(30, 1, 8, 30, 30, '', '2026-01-01 12:00:00', '', 'synthetic-control');
$sync = array('qaction' => 'compare', 'qid' => 6, 'pref' => json_encode(array($activity)));
check_web(WebRequestAuth::sync_scope($driverIdentity, $sync, $belongs), 'Own activity denied');
foreach (array(0, 2, 3, 4) as $index) {
    $bad = $activity; $bad[$index] = 99; $sync['pref'] = json_encode(array($bad));
    check_web(!WebRequestAuth::sync_scope($driverIdentity, $sync, $belongs), 'Foreign nested reference accepted');
}
$bad = $activity; $bad[3] = 0; $bad[8] = 'foreign-control'; $sync['pref'] = json_encode(array($bad));
check_web(!WebRequestAuth::sync_scope($driverIdentity, $sync, $belongs), 'Foreign temporary journey accepted');
$sync['pref'] = json_encode(array(array(0, 0, 0, 0, 0, '', null, null, '')));
check_web(WebRequestAuth::sync_scope($driverIdentity, $sync, $belongs), 'Empty activity sentinel rejected');

require_once __DIR__ . '/../app/code/base/core.php';
class WebResultDouble {
    public function fetch_assoc() { return array('user' => 7, 'company' => 20, 'type' => 1); }
}
class WebStatementDouble {
    public $closed = false;
    public function bind_param($types, &$id, &$token) {
        check_web($types === 'is' && $id === 7 && $token === str_repeat('a', 32), 'Unexpected session parameters');
        return true;
    }
    public function execute() { return true; }
    public function get_result() { return new WebResultDouble; }
    public function close() { $this->closed = true; }
}
class WebConnectionDouble {
    public $statement;
    public function prepare($sql) {
        check_web(strpos($sql, 'SELECT ') === 0 && strpos($sql, 'iSESuserid = ? AND s.sSEShash = ?') !== false, 'Session query not parameterized');
        check_web(strpos($sql, 's.iSESexp > 0') !== false && strpos($sql, '> UNIX_TIMESTAMP()') !== false, 'Expiry check missing');
        check_web(strpos($sql, 'u.iUSRstatus = 1 AND u.iUSRdel = 0') !== false, 'Account validation missing');
        $this->statement = new WebStatementDouble;
        return $this->statement;
    }
}
$reflection = new ReflectionClass('Core');
$instance = $reflection->getProperty('instance'); $instance->setAccessible(true);
$instance->setValue(null, $reflection->newInstanceWithoutConstructor());
Core::$mysqli = new WebConnectionDouble;
foreach (array('admin', 'public') as $role) {
    check_web(WebRequestAuth::lookup($role, 7, $token)['user'] === 7, 'Session lookup result missing');
    check_web(Core::$mysqli->statement->closed, 'Session statement leaked');
}
$instance->setValue(null, null); Core::$mysqli = null;
echo "Passed $checks web-auth checks without database connections.\n";

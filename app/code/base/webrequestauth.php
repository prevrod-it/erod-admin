<?php
class WebRequestAuth
{
    public static function role($endpoint)
    {
        $admin = array('adminentity.php', 'adminopunit.php', 'adminplans.php', 'adminpubusers.php', 'adminvehicles.php');
        $public = array('pubadminentity.php', 'pubadminopunit.php', 'pubadminpubusers.php', 'pubadminvehicles.php', 'usersapi.php', 'reportapi.php');
        if (in_array($endpoint, $admin, true)) { return 'admin'; }
        if (in_array($endpoint, $public, true)) { return 'public'; }
        return null;
    }

    public static function csrf($role, $user, $session)
    {
        if (!in_array($role, array('admin', 'public'), true) || !self::id($user)
            || !is_string($session) || !preg_match('/\A[a-f0-9]{32}\z/', $session)) { return ''; }
        return hash_hmac('sha256', 'erod-web-csrf|' . $role . '|' . (int)$user, $session);
    }

    public static function id($value)
    {
        if (!is_string($value) && !is_int($value)) { return false; }
        return filter_var($value, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 2147483647)));
    }

    // Pure gate: the database callback is reached only after key/cookie/CSRF validation.
    public static function authenticate($post, $cookies, $endpoint, $key, $lookup)
    {
        $role = self::role($endpoint);
        if ($role === null || !is_string($key) || $key === '' || !isset($post['reqcode'])
            || !is_string($post['reqcode']) || !hash_equals($key, $post['reqcode'])) { return false; }
        $cookie = isset($cookies[$role . 'activesession']) ? $cookies[$role . 'activesession'] : null;
        if (!is_string($cookie) || !preg_match('/\A([1-9][0-9]*)\|([a-f0-9]{32})\z/', $cookie, $parts)
            || !($user = self::id($parts[1]))) { return false; }
        $csrf = self::csrf($role, $user, $parts[2]);
        if (!isset($post['csrf']) || !is_string($post['csrf']) || !hash_equals($csrf, $post['csrf'])) { return false; }
        $identity = $lookup($role, $user, $parts[2]);
        if (!is_array($identity) || (int)$identity['user'] !== $user) { return false; }
        $identity['role'] = $role;
        if ($role === 'public') {
            if (strpos($endpoint, 'pubadmin') === 0 && (int)$identity['type'] > 1) { return false; }
            if (in_array($endpoint, array('usersapi.php', 'reportapi.php'), true)
                && ((int)$identity['type'] < 2 || !isset($post['userid']) || self::id($post['userid']) !== $user)) { return false; }
        }
        return $identity;
    }

    private static function row($db, $sql, $types, $values)
    {
        $stmt = $db->prepare($sql);
        if (!$stmt) { return false; }
        try {
            if (!$stmt->bind_param($types, ...$values) || !$stmt->execute()) { return false; }
            $result = $stmt->get_result();
            return $result ? $result->fetch_assoc() : false;
        } finally { $stmt->close(); }
    }

    public static function lookup($role, $user, $session)
    {
        Core::init();
        if ($role === 'admin') {
            $sql = "SELECT u.iUSRid AS user, u.iUSRgroup AS usergroup FROM sessions s JOIN users u ON u.iUSRid = s.iSESuserid WHERE s.iSESuserid = ? AND s.sSEShash = ? AND s.iSESexp > 0 AND UNIX_TIMESTAMP(s.dSESstart) + s.iSESexp > UNIX_TIMESTAMP() AND u.iUSRstatus = 1 AND u.iUSRdel = 0 LIMIT 1";
        } else {
            $sql = "SELECT u.iUSRid AS user, u.iUSRtype AS type, u.iUSRcompanyid AS company FROM publicsessions s JOIN publicusers u ON u.iUSRid = s.iSESuserid JOIN entities e ON e.iENTid = u.iUSRcompanyid WHERE s.iSESuserid = ? AND s.sSEShash = ? AND s.iSESexp > 0 AND UNIX_TIMESTAMP(s.dSESstart) + s.iSESexp > UNIX_TIMESTAMP() AND u.iUSRstatus = 1 AND u.iUSRdel = 0 AND e.iENTstatus = 1 AND e.iENTdel = 0 LIMIT 1";
        }
        return self::row(Core::$mysqli, $sql, 'is', array($user, $session));
    }

    public static function owns($db, $kind, $id, $company)
    {
        $tables = array('entity' => array('entities', 'iENTid', 'iENTid', 'iENTdel'),
            'opunit' => array('opunits', 'iENTid', 'iENTentityid', 'iENTdel'),
            'vehicle' => array('vehicles', 'iVHCid', 'iVHCcompanyid', 'iVHCdel'),
            'plan' => array('plans', 'iPLNid', 'iPLNentityid', 'iPLNdel'),
            'pubuser' => array('publicusers', 'iUSRid', 'iUSRcompanyid', 'iUSRdel'));
        if (!isset($tables[$kind]) || !self::id($id)) { return false; }
        list($table, $pk, $owner, $deleted) = $tables[$kind];
        return (bool)self::row($db, "SELECT $pk FROM $table WHERE $pk = ? AND $owner = ? AND $deleted = 0 LIMIT 1", 'ii', array((int)$id, (int)$company));
    }

    public static function scope($identity, $endpoint, $post, $owns)
    {
        if ($identity['role'] === 'admin') { return true; } // Existing global-admin access model.
        if (in_array($endpoint, array('usersapi.php', 'reportapi.php'), true)) { return true; } // Self checked above.
        $company = (int)$identity['company'];
        $map = array('pubadminentity.php' => array('entity', null), 'pubadminopunit.php' => array('opunit', 'entityid'),
            'pubadminvehicles.php' => array('vehicle', 'entity'), 'pubadminpubusers.php' => array('pubuser', 'entityid'));
        if (!isset($map[$endpoint]) || $company < 1) { return false; }
        list($kind, $companyField) = $map[$endpoint];
        $action = isset($post['formaction']) ? $post['formaction'] : '';
        if (!in_array($action, array('insert', 'update', 'delete'), true)) { return false; }
        if ($kind === 'entity' && $action !== 'update') { return false; }
        if ($companyField !== null && (!isset($post[$companyField]) || self::id($post[$companyField]) !== $company)) { return false; }
        if ($action !== 'insert' && (!isset($post['itemid']) || !($id = self::id($post['itemid'])) || !$owns($kind, $id, $company))) { return false; }
        if ($kind === 'entity' && isset($post['planid']) && (string)$post['planid'] !== '0'
            && (!($id = self::id($post['planid'])) || !$owns('plan', $id, $company))) { return false; }
        if ($kind === 'pubuser' && $action !== 'delete' && isset($post['opunit']) && (string)$post['opunit'] !== '0'
            && (!($id = self::id($post['opunit'])) || !$owns('opunit', $id, $company))) { return false; }
        return true;
    }

    public static function require_request($endpoint)
    {
        try {
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { self::deny(405); }
            $identity = self::authenticate($_POST, $_COOKIE, $endpoint, Core::api_key('web'), array(__CLASS__, 'lookup'));
            if (!$identity) { self::deny(401); }
            if (!self::scope($identity, $endpoint, $_POST, function ($kind, $id, $company) {
                return self::owns(Core::$mysqli, $kind, $id, $company);
            })) { self::deny(403); }
            if ($endpoint === 'usersapi.php' && !self::sync_scope($identity, $_POST, function ($kind, $id) use ($identity) {
                return self::driver_owns(Core::$mysqli, $identity, $kind, $id);
            })) { self::deny(403); }
            // Form handlers' userid is caller metadata; driver APIs' userid was verified as self.
            $_POST['userid'] = $identity['user'];
            return $identity;
        } catch (Exception $error) { self::deny(403); }
    }

    public static function driver_owns($db, $identity, $kind, $id)
    {
        $queries = array(
            'journey' => "SELECT iJRNid FROM journeys WHERE iJRNid = ? AND iJRNuserid = ? AND iJRNdel = 0 LIMIT 1",
            'control' => "SELECT iJRNid FROM journeys WHERE sJRNuident = ? AND iJRNuserid = ? AND iJRNdel = 0 LIMIT 1",
            'activity' => "SELECT iACTid FROM activities WHERE iACTid = ? AND iACTuserid = ? AND iACTdel = 0 LIMIT 1",
            'remark' => "SELECT iRMKid FROM userremarks WHERE iRMKid = ? AND iRMKuserid = ? LIMIT 1",
            'allocation' => "SELECT a.iVALid FROM vehalloc a JOIN journeys j ON j.iJRNid = a.iVALjourney WHERE a.iVALid = ? AND j.iJRNuserid = ? AND a.iVALdel = 0 LIMIT 1");
        if ($kind === 'vehicle') { return self::owns($db, 'vehicle', $id, $identity['company']); }
        if ($kind === 'coworker') { return self::owns($db, 'pubuser', $id, $identity['company']); }
        if (!isset($queries[$kind])) { return false; }
        return (bool)self::row($db, $queries[$kind], $kind === 'control' ? 'si' : 'ii', array($id, (int)$identity['user']));
    }

    public static function sync_scope($identity, $post, $belongs)
    {
        if (!isset($post['qaction']) || $post['qaction'] !== 'compare') { return true; }
        $qid = isset($post['qid']) ? self::id($post['qid']) : false;
        if (!in_array($qid, array(3, 4, 5, 6, 7), true) || !isset($post['pref']) || !is_string($post['pref'])) { return false; }
        $records = json_decode($post['pref'], true);
        if (!is_array($records)) { return false; }
        $kinds = array(3 => 'vehicle', 4 => 'journey', 5 => 'allocation', 6 => 'activity', 7 => 'remark');
        $references = array(3 => array(), 4 => array(3 => 'coworker'), 5 => array(1 => 'vehicle', 3 => 'journey'),
            6 => array(3 => 'journey', 4 => 'vehicle'), 7 => array(2 => 'journey'));
        foreach ($records as $record) {
            if (!is_array($record) || !array_key_exists(0, $record)) { return false; }
            $refs = array(0 => $kinds[$qid]) + $references[$qid];
            foreach ($refs as $index => $kind) {
                if (!array_key_exists($index, $record) || (!is_int($record[$index]) && !is_string($record[$index]))) { return false; }
                if ((string)$record[$index] === '0') { continue; }
                $id = self::id($record[$index]);
                if (!$id || !$belongs($kind, $id)) { return false; }
            }
            // A zero journey may reference a previously synced journey by its temporary control string.
            $journeyIndex = $qid === 7 ? 2 : 3;
            $controlIndex = array(5 => 13, 6 => 8, 7 => 5);
            if (isset($controlIndex[$qid]) && (string)$record[$journeyIndex] === '0') {
                $control = isset($record[$controlIndex[$qid]]) ? $record[$controlIndex[$qid]] : '';
                if (!is_string($control) || ($control !== '' && !$belongs('control', $control))) { return false; }
            }
            // Preserve the existing zero-user empty-list sentinels; other mismatches never reach writes.
            $userIndex = array(4 => 2, 6 => 2, 7 => 1);
            if (isset($userIndex[$qid])) {
                $value = isset($record[$userIndex[$qid]]) ? $record[$userIndex[$qid]] : null;
                if (!is_int($value) && !is_string($value)) { return false; }
                if ((string)$value === '0' && (string)$record[0] === '0') {
                    $dateIndex = array(4 => 4, 6 => 6, 7 => 3);
                    if (!empty($record[$dateIndex[$qid]])) { return false; }
                } elseif (self::id($value) !== (int)$identity['user']) { return false; }
            }
        }
        return true;
    }

    private static function deny($status)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode(array(array(0, 'Pedido não autorizado. Atualize a página ou inicie sessão novamente.')));
        exit;
    }
}

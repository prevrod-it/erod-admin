// Uses the actual browser encoder and PHP decoder, with synthetic input and no DB.
const fs = require('fs');
const vm = require('vm');
const { spawnSync } = require('child_process');
const path = require('path');
const root = path.resolve(__dirname, '..');
const encoderPath = process.argv.includes('--public')
    ? 'skin/publichtml/js/publicautentication.js'
    : 'skin/adminhtml/js/adminautentication.js';
const source = fs.readFileSync(path.join(root, encoderPath), 'utf8');
const start = source.indexOf('  function encrypt(str)');
const end = source.indexOf('  /*----------  SEND LOGIN FORM', start);
if (start < 0 || end < 0) throw new Error('Browser encoder not found');
const now = Date.now();
const plain = 'SyntheticLogin42!';
function encode(offset) {
    const context = { Date: class extends Date { constructor() { super(now + offset * 1000); } } };
    vm.createContext(context);
    vm.runInContext(source.slice(start, end), context);
    return context.encrypt(plain);
}
const payload = { plain, current: encode(0), previous: encode(-60), future: encode(60), stale: encode(-180) };
const php = String.raw`
require 'app/code/base/core.php';
$data = json_decode(stream_get_contents(STDIN), true);
$core = (new ReflectionClass('Core'))->newInstanceWithoutConstructor();
$checks = 0;
foreach (array('UTC', 'Europe/Lisbon', 'Asia/Tokyo') as $zone) {
    date_default_timezone_set($zone);
    foreach (array('current', 'previous', 'future') as $field) {
        if ($core->decrypt_pwd($data[$field], true) !== $data['plain']) {
            throw new Exception('UTC web decoding failed: ' . $zone . ' / ' . $field);
        }
        $checks++;
    }
    if ($core->decrypt_pwd($data['stale'], true) !== '_ERROR_') { throw new Exception('Stale code accepted'); }
    $bad = $data['current'];
    $bad[1] = '9';
    if ($core->decrypt_pwd($bad, true) !== '_ERROR_') { throw new Exception('Bad sequence accepted'); }
    $checks += 2;
}
date_default_timezone_set('UTC');
if ($core->decrypt_pwd($data['current']) !== $data['plain']) { throw new Exception('Legacy decoder changed'); }
if ($core->decrypt_pwd($data['previous']) !== '_ERROR_') { throw new Exception('Legacy time tolerance changed'); }
if (Core::$mysqli !== null) { throw new Exception('Unexpected database connection'); }
echo 'Passed ', $checks + 3, " checks using actual browser encoder; no DB connections.\n";
`;
const result = spawnSync('php', ['-r', php], { cwd: root, input: JSON.stringify(payload), encoding: 'utf8' });
if (result.error) throw result.error;
process.stdout.write(result.stdout || '');
process.stderr.write(result.stderr || '');
process.exitCode = result.status === 0 ? 0 : 1;

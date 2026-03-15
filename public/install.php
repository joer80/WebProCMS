<?php

declare(strict_types=1);

/**
 * WebProCMS Installer
 *
 * Included automatically by public/index.php when the app is not yet configured.
 * After RunCloud deploys the repo, just visit your domain — this runs automatically.
 */
$appRoot = dirname(__DIR__);
$envFile = $appRoot.'/.env';
$envExample = $appRoot.'/.env.example';

// Refuse to run if already installed
if (file_exists($envFile) && preg_match('/^APP_KEY=.+/m', file_get_contents($envFile))) {
    header('Location: /dashboard');
    exit;
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function installer_run(array|string $command, string $cwd, array $env = []): string
{
    // Pass as a shell string so /bin/sh handles PATH resolution — proc_open with
    // an array uses execve which requires an absolute path for the executable.
    $shellCmd = is_string($command) ? $command : implode(' ', array_map('escapeshellarg', $command));
    $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $process = proc_open($shellCmd, $descriptors, $pipes, $cwd, $env ?: null);

    if (! is_resource($process)) {
        throw new \RuntimeException('Failed to start process (proc_open returned false). Check PHP disabled_functions in RunCloud.');
    }

    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        throw new \RuntimeException(trim($error ?: $output ?: 'Exit code '.$exitCode));
    }

    return trim($output);
}

function installer_base_env(): array
{
    $home = getenv('HOME') ?: '/tmp';

    return [
        'PATH' => '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:'.(getenv('PATH') ?: ''),
        'HOME' => $home,
        'COMPOSER_HOME' => $home.'/.composer',
    ];
}

function installer_find_php(): string
{
    // PHP_BINARY in FPM points to the FPM binary, not the CLI — find the CLI instead.
    foreach (['/usr/bin/php', '/usr/local/bin/php'] as $path) {
        if (is_executable($path)) {
            return $path;
        }
    }

    return 'php';
}

function installer_find_composer(): string
{
    foreach (['/usr/local/bin/composer', '/usr/bin/composer'] as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return 'composer';
}

function installer_find_npm(): string
{
    $home = getenv('HOME') ?: '';

    foreach ([
        $home.'/Library/Application Support/Herd/config/nvm/versions/node/*/bin/npm',
        $home.'/.nvm/versions/node/*/bin/npm',
        '/usr/local/bin/npm',
        '/usr/bin/npm',
    ] as $pattern) {
        $matches = glob($pattern);
        if (! empty($matches)) {
            return end($matches);
        }
    }

    return 'npm';
}

function installer_set_env(string $file, string $key, string $value): void
{
    $content = file_get_contents($file);

    // Quote values that contain spaces, hashes, or special shell characters
    $escaped = ($value === '' || preg_match('/[\s#"\'\\\\]/', $value))
        ? '"'.str_replace('"', '\\"', $value).'"'
        : $value;

    $line = $key.'='.$escaped;

    if (preg_match('/^'.preg_quote($key, '/').'=/m', $content)) {
        $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $content);
    } else {
        $content .= "\n".$line;
    }

    file_put_contents($file, $content);
}

function installer_step(string $label, callable $fn): bool
{
    echo '<div class="step"><div class="step-row">';
    echo '<span class="step-label"><span class="arr">▶</span> '.htmlspecialchars($label).'</span>';
    ob_flush();
    flush();

    try {
        $output = $fn();
        echo '<span class="badge ok">✓ Done</span></div>';
        if (is_string($output) && $output !== '') {
            echo '<pre class="step-out">'.htmlspecialchars($output).'</pre>';
        }
        echo '</div>';
        ob_flush();
        flush();

        return true;
    } catch (\Throwable $e) {
        echo '<span class="badge err">✗ Failed</span></div>';
        echo '<pre class="step-out err">'.htmlspecialchars($e->getMessage()).'</pre>';
        echo '</div>';
        ob_flush();
        flush();

        return false;
    }
}

// ---------------------------------------------------------------------------
// POST — run installation
// ---------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appName = trim($_POST['app_name'] ?? 'My Website');
    $appUrl = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $mailFrom = trim($_POST['mail_from'] ?? '') ?: $adminEmail;
    $dbDriver = ($_POST['db_driver'] ?? 'sqlite') === 'mysql' ? 'mysql' : 'sqlite';
    $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    $seedDemo = isset($_POST['seed_demo']);

    set_time_limit(300);

    header('Content-Type: text/html; charset=utf-8');
    header('X-Accel-Buffering: no'); // disable nginx buffering
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    ob_implicit_flush(true);

    echo installer_css().'<div class="card"><div class="logo">WebProCMS</div>';
    echo '<h1>Installing&hellip;</h1><div class="log">';
    flush();

    $failed = false;

    // Pre-flight — verify proc_open is available
    if (! function_exists('proc_open')) {
        echo '<div class="step"><div class="step-row"><span class="step-label"><span class="arr">▶</span> Checking server requirements</span><span class="badge err">✗ Failed</span></div>';
        echo '<pre class="step-out err">proc_open is disabled in PHP. In RunCloud go to Server → PHP Settings → Disabled Functions, replace the list with the one shown on the previous page, save, then reload this page.</pre></div>';
        echo '</div></div></body></html>';
        exit;
    }

    $baseEnv = installer_base_env();
    $php = installer_find_php();

    // 0a — Composer install (skipped if vendor/ already exists)
    if (! $failed && ! is_dir($appRoot.'/vendor') && ! installer_step('Installing PHP dependencies', function () use ($appRoot, $baseEnv) {
        $composer = installer_find_composer();

        return installer_run([$composer, 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'], $appRoot, $baseEnv);
    })) {
        $failed = true;
    }

    // 0b — Storage symlink
    if (! $failed && ! installer_step('Creating storage symlink', function () use ($appRoot, $baseEnv, $php) {
        installer_run([$php, 'artisan', 'storage:link', '--no-interaction'], $appRoot, $baseEnv);
    })) {
        $failed = true;
    }

    // 0c — npm build (skipped if public/build/ already exists)
    if (! $failed && ! is_dir($appRoot.'/public/build') && ! installer_step('Building frontend assets', function () use ($appRoot, $baseEnv) {
        $npm = installer_find_npm();
        $nodeBin = dirname($npm);
        $env = array_merge($baseEnv, ['PATH' => $nodeBin.':'.$appRoot.'/node_modules/.bin:'.$baseEnv['PATH']]);

        return installer_run(escapeshellarg($npm).' run build', $appRoot, $env);
    })) {
        $failed = true;
    }

    // 1 — Copy .env
    if (! $failed && ! installer_step('Creating .env from template', function () use ($envFile, $envExample) {
        if (! file_exists($envExample)) {
            throw new \RuntimeException('.env.example not found — is the repo fully deployed?');
        }
        copy($envExample, $envFile);
    })) {
        $failed = true;
    }

    // 2 — Write configuration values
    if (! $failed && ! installer_step('Writing configuration', function () use (
        $envFile, $appName, $appUrl, $adminEmail, $mailFrom,
        $dbDriver, $dbHost, $dbPort, $dbName, $dbUser, $dbPass
    ) {
        installer_set_env($envFile, 'APP_NAME', $appName);
        installer_set_env($envFile, 'APP_URL', $appUrl);
        installer_set_env($envFile, 'APP_ENV', 'production');
        installer_set_env($envFile, 'APP_DEBUG', 'false');
        installer_set_env($envFile, 'BUSINESS_ADMIN_EMAIL', $adminEmail);
        installer_set_env($envFile, 'MAIL_FROM_ADDRESS', $mailFrom);
        installer_set_env($envFile, 'QUEUE_CONNECTION', 'database');
        installer_set_env($envFile, 'REBUILD_ASSETS_LOCALLY', 'false');
        installer_set_env($envFile, 'CMS_GIT_BRANCH', 'main');
        installer_set_env($envFile, 'DB_CONNECTION', $dbDriver);
        if ($dbDriver === 'mysql') {
            installer_set_env($envFile, 'DB_HOST', $dbHost);
            installer_set_env($envFile, 'DB_PORT', $dbPort);
            installer_set_env($envFile, 'DB_DATABASE', $dbName);
            installer_set_env($envFile, 'DB_USERNAME', $dbUser);
            installer_set_env($envFile, 'DB_PASSWORD', $dbPass);
        }
    })) {
        $failed = true;
    }

    // 3 — Generate app key
    if (! $failed && ! installer_step('Generating application key', function () use ($appRoot, $baseEnv, $php) {
        installer_run([$php, 'artisan', 'key:generate', '--force'], $appRoot, $baseEnv);
    })) {
        $failed = true;
    }

    // 4 — Database setup
    if (! $failed) {
        if ($dbDriver === 'sqlite') {
            if (! installer_step('Creating SQLite database file', function () use ($appRoot) {
                $path = $appRoot.'/database/database.sqlite';
                if (! file_exists($path)) {
                    touch($path);
                }
            })) {
                $failed = true;
            }
        } else {
            if (! installer_step('Testing MySQL connection', function () use ($dbHost, $dbPort, $dbName, $dbUser, $dbPass) {
                try {
                    new \PDO(
                        "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
                        $dbUser,
                        $dbPass,
                        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
                    );
                } catch (\PDOException $e) {
                    throw new \RuntimeException('MySQL connection failed: '.$e->getMessage());
                }
            })) {
                $failed = true;
            }
        }
    }

    // 5 — Migrate
    if (! $failed && ! installer_step('Running database migrations', function () use ($appRoot, $baseEnv, $php) {
        return installer_run([$php, 'artisan', 'migrate', '--force', '--no-interaction'], $appRoot, $baseEnv);
    })) {
        $failed = true;
    }

    // 6 — Seed (optional)
    if (! $failed && $seedDemo && ! installer_step('Seeding demo content', function () use ($appRoot, $baseEnv, $php) {
        return installer_run([$php, 'artisan', 'db:seed', '--no-interaction'], $appRoot, $baseEnv);
    })) {
        $failed = true;
    }

    // 7 — Optimize
    if (! $failed && ! installer_step('Caching config and routes', function () use ($appRoot, $baseEnv, $php) {
        return installer_run([$php, 'artisan', 'optimize', '--no-interaction'], $appRoot, $baseEnv);
    })) {
        $failed = true;
    }

    echo '</div>'; // .log

    // Remove .env on failure so the installer can be retried cleanly
    if ($failed) {
        if (file_exists($envFile)) {
            unlink($envFile);
        }
        echo '<div class="result err-result">
            <h2>&#10060; Installation failed</h2>
            <p>The .env file has been removed so you can retry.<br>
            Fix the error shown above, then <a href="/">reload this page</a>.</p>
        </div>';
    } else {
        $dashUrl = htmlspecialchars($appUrl).'/dashboard';
        echo '<div class="result ok-result">
            <h2>&#9989; WebProCMS installed!</h2>
            <div class="creds">
                <p><strong>Email:</strong> '.htmlspecialchars($adminEmail).'</p>
                <p><strong>Password:</strong> Admin &nbsp;<span class="hint">you\'ll be prompted to change it on first login</span></p>
            </div>
            <div class="notice">
                &#9888;&#65039; Set up a <strong>Queue Worker</strong> in RunCloud &rarr; your server &rarr; Processes.
                The CMS update button and background jobs require it.
            </div>
            <div class="notice">
                &#128161; In RunCloud, go to your web app &rarr; <strong>Settings</strong> and change the <strong>Application Stack</strong> to <strong>Laravel</strong>. This gives you one-click access to run Artisan commands directly from the RunCloud panel.
            </div>
            <p class="redir">Redirecting in <span id="ct">5</span>s&hellip; <a href="'.$dashUrl.'">go now</a></p>
        </div>
        <script>let t=5;const el=document.getElementById("ct");const iv=setInterval(()=>{t--;el&&(el.textContent=t);t<=0&&(clearInterval(iv),location="'.$dashUrl.'")},1000);</script>';
    }

    echo '</div></body></html>';
    exit;
}

// ---------------------------------------------------------------------------
// GET — show setup form
// ---------------------------------------------------------------------------

$proto = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$defaultUrl = $proto.'://'.($_SERVER['HTTP_HOST'] ?? 'localhost');

$disabledFunctions = 'getmyuid,passthru,leak,listen,diskfreespace,tmpfile,link,shell_exec,dl,exec,system,highlight_file,source,show_source,fpassthru,virtual,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix_getppid,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,pcntl_exec,socket_accept,socket_bind,socket_clear_error,socket_close,socket_connect,posix_geteuid,ini_alter,socket_listen,socket_create_listen,socket_read,socket_create_pair,stream_socket_server,escapeshellcmd,ini_alter,popen,symlink';

echo installer_css().'<div class="card">
<div class="logo">WebProCMS</div>
<p class="tagline">Complete these two steps in RunCloud before installing.</p>
<div class="prereqs">
    <div class="prereq">
        <div class="prereq-num">1</div>
        <div class="prereq-body">
            <strong>Issue your SSL certificate</strong>
            <p>Go to your web app &rarr; <strong>SSL/TLS</strong> and issue a Let&rsquo;s Encrypt certificate. The installer sets <code>APP_URL</code> to <code>https://</code> &mdash; without SSL the redirect to the dashboard will fail.</p>
        </div>
    </div>
    <div class="prereq">
        <div class="prereq-num">2</div>
        <div class="prereq-body">
            <strong>Update PHP disabled functions</strong>
            <p>Go to <strong>Server &rarr; PHP Settings &rarr; Disabled Functions</strong> and replace the entire list with the one below. This removes the functions WebProCMS requires (<code>proc_open</code>, <code>set_time_limit</code>, <code>ignore_user_abort</code>) while keeping dangerous ones blocked.</p>
            <div class="fn-wrap">
                <textarea id="fn-list" class="fn-list" readonly>'.htmlspecialchars($disabledFunctions).'</textarea>
                <button type="button" class="fn-copy" onclick="navigator.clipboard.writeText(document.getElementById(\'fn-list\').value);this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'Copy\',2000)">Copy</button>
            </div>
        </div>
    </div>
</div>
<hr class="divider">
<p class="form-intro">Now fill in your site details below.</p>
<form method="POST" onsubmit="this.querySelector(\'.btn\').disabled=true;this.querySelector(\'.btn\').textContent=\'Installing…\'">

    <fieldset>
        <legend>Site Setup</legend>
        <div class="field">
            <label for="app_name">Site Name</label>
            <input type="text" id="app_name" name="app_name" value="My Website" required>
        </div>
        <div class="field">
            <label for="app_url">Site URL <span class="hint">— no trailing slash</span></label>
            <input type="url" id="app_url" name="app_url" value="'.htmlspecialchars($defaultUrl).'" required>
        </div>
        <div class="field">
            <label for="admin_email">Admin Email</label>
            <input type="email" id="admin_email" name="admin_email" placeholder="you@yourdomain.com" required>
        </div>
        <div class="field">
            <label for="mail_from">Mail From Address <span class="hint">— defaults to admin email if blank</span></label>
            <input type="email" id="mail_from" name="mail_from" placeholder="noreply@yourdomain.com">
        </div>
    </fieldset>

    <fieldset>
        <legend>Database</legend>
        <div class="radio-group">
            <label class="radio-card active" id="card-sqlite">
                <input type="radio" name="db_driver" value="sqlite" checked>
                <strong>SQLite</strong>
                <span class="hint">Recommended for most sites — zero config</span>
            </label>
            <label class="radio-card" id="card-mysql">
                <input type="radio" name="db_driver" value="mysql">
                <strong>MySQL</strong>
                <span class="hint">For high-traffic or multi-app setups</span>
            </label>
        </div>
        <div id="mysql-fields" style="display:none;margin-top:.75rem">
            <div class="field-row">
                <div class="field">
                    <label for="db_host">Host</label>
                    <input type="text" id="db_host" name="db_host" value="127.0.0.1">
                </div>
                <div class="field field-sm">
                    <label for="db_port">Port</label>
                    <input type="number" id="db_port" name="db_port" value="3306">
                </div>
            </div>
            <div class="field">
                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" placeholder="webprocms">
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="db_user">Username</label>
                    <input type="text" id="db_user" name="db_user">
                </div>
                <div class="field">
                    <label for="db_pass">Password</label>
                    <input type="password" id="db_pass" name="db_pass">
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Options</legend>
        <label class="check-row">
            <input type="checkbox" name="seed_demo">
            <span>Seed demo content <span class="hint">(posts, categories, locations)</span></span>
        </label>
    </fieldset>

    <button type="submit" class="btn">Install WebProCMS &rarr;</button>
</form>
</div>
<script>
const radios=document.querySelectorAll(\'input[name=db_driver]\');
const mf=document.getElementById(\'mysql-fields\');
const cs=document.getElementById(\'card-sqlite\');
const cm=document.getElementById(\'card-mysql\');
radios.forEach(r=>r.addEventListener(\'change\',()=>{
    const mysql=document.querySelector(\'input[name=db_driver]:checked\').value===\'mysql\';
    mf.style.display=mysql?\'block\':\'none\';
    cs.classList.toggle(\'active\',!mysql);
    cm.classList.toggle(\'active\',mysql);
}));
</script>
</body></html>';
exit;

// ---------------------------------------------------------------------------
// CSS
// ---------------------------------------------------------------------------

function installer_css(): string
{
    return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>WebProCMS Installer</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f1f5f9;color:#1e293b;min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:2.5rem 1rem}
.card{background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.08),0 8px 24px rgba(0,0,0,.06);padding:2.5rem;width:100%;max-width:560px}
.logo{font-size:1.2rem;font-weight:800;color:#3B4A99;letter-spacing:-.5px;margin-bottom:.2rem}
.tagline{font-size:.875rem;color:#64748b;margin-bottom:2rem}
h1{font-size:1.35rem;font-weight:700;margin-bottom:1.5rem}
fieldset{border:none;margin-bottom:1.5rem}
legend{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;display:block;width:100%;padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid #f1f5f9}
.field{margin-bottom:.875rem}
label{display:block;font-size:.875rem;font-weight:500;color:#374151;margin-bottom:.35rem}
.hint{font-weight:400;color:#94a3b8;font-size:.78rem}
input[type=text],input[type=email],input[type=url],input[type=password],input[type=number]{width:100%;padding:.625rem .875rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;color:#1e293b;outline:none;transition:border-color .15s,box-shadow .15s}
input:focus{border-color:#3B4A99;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.radio-group{display:grid;grid-template-columns:1fr 1fr;gap:.625rem}
.radio-card{display:flex;flex-direction:column;gap:.2rem;border:2px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;cursor:pointer;transition:border-color .15s,background .15s}
.radio-card input{display:none}
.radio-card.active{border-color:#3B4A99;background:#eff6ff}
.radio-card strong{font-size:.875rem}
.field-row{display:grid;grid-template-columns:1fr auto;gap:.75rem}
.field-sm{width:90px}
.check-row{display:flex;align-items:center;gap:.625rem;font-size:.875rem;color:#374151;cursor:pointer;user-select:none}
.check-row input{width:1rem;height:1rem;accent-color:#3B4A99;cursor:pointer;flex-shrink:0}
.btn{width:100%;padding:.8rem;background:#3B4A99;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:600;cursor:pointer;margin-top:.25rem;transition:background .15s}
.btn:hover:not(:disabled){background:#2f3d80}
.btn:disabled{opacity:.7;cursor:not-allowed}
/* log */
.log{display:flex;flex-direction:column;gap:.25rem;margin-bottom:1rem}
.step{border:1px solid #f1f5f9;border-radius:8px;overflow:hidden;background:#f8fafc;margin-bottom:.25rem}
.step-row{display:flex;align-items:center;gap:.75rem;padding:.6rem .875rem}
.arr{color:#94a3b8;font-size:.8rem}
.step-label{flex:1;font-size:.83rem;font-family:ui-monospace,monospace;color:#475569}
.badge{font-size:.72rem;font-weight:700;border-radius:20px;padding:.2rem .6rem;white-space:nowrap}
.badge.ok{background:#dcfce7;color:#166534}
.badge.err{background:#fee2e2;color:#991b1b}
.step-out{font-size:.72rem;font-family:ui-monospace,monospace;padding:.5rem .875rem;white-space:pre-wrap;word-break:break-word;color:#64748b;background:#f1f5f9;border-top:1px solid #e2e8f0;max-height:180px;overflow-y:auto}
.step-out.err{background:#fff5f5;color:#dc2626;border-color:#fecaca}
/* result */
.result{border-radius:12px;padding:1.75rem;text-align:center;margin-top:.25rem}
.ok-result{background:#f0fdf4;border:1px solid #bbf7d0}
.err-result{background:#fff5f5;border:1px solid #fecaca}
.result h2{font-size:1.2rem;font-weight:700;margin-bottom:.875rem}
.ok-result h2{color:#166534}.err-result h2{color:#dc2626}
.creds{background:#fff;border:1px solid #d1fae5;border-radius:8px;display:inline-block;padding:.875rem 1.25rem;margin:.25rem 0 1rem;text-align:left;font-size:.875rem}
.creds p{margin:.2rem 0}.creds strong{color:#166534}
.notice{background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:.75rem 1rem;font-size:.82rem;color:#92400e;margin-bottom:1rem;text-align:left}
.redir{font-size:.85rem;color:#64748b}
a{color:#3B4A99;text-decoration:none}a:hover{text-decoration:underline}
/* prereqs */
.prereqs{display:flex;flex-direction:column;gap:.875rem;margin-bottom:1.25rem}
.prereq{display:flex;gap:.875rem;align-items:flex-start}
.prereq-num{flex-shrink:0;width:1.75rem;height:1.75rem;background:#3B4A99;color:#fff;border-radius:50%;font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin-top:.1rem}
.prereq-body{flex:1;font-size:.875rem;color:#374151}
.prereq-body strong{display:block;font-weight:600;margin-bottom:.25rem}
.prereq-body p{color:#64748b;font-size:.82rem;line-height:1.5;margin-bottom:.5rem}
.prereq-body code{font-family:ui-monospace,monospace;font-size:.78rem;background:#f1f5f9;padding:.1em .35em;border-radius:4px}
.fn-wrap{position:relative}
.fn-list{width:100%;height:3.5rem;font-family:ui-monospace,monospace;font-size:.7rem;color:#475569;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:8px;padding:.5rem .75rem;resize:none;overflow-y:auto;padding-right:4.5rem}
.fn-copy{position:absolute;top:.4rem;right:.4rem;padding:.25rem .65rem;background:#3B4A99;color:#fff;border:none;border-radius:6px;font-size:.72rem;font-weight:600;cursor:pointer}
.fn-copy:hover{background:#2f3d80}
.divider{border:none;border-top:1px solid #f1f5f9;margin:1.25rem 0}
.form-intro{font-size:.875rem;color:#64748b;margin-bottom:1.25rem}
</style>
</head>
<body>';
}

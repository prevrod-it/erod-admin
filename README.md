# E-ROD administration and app API

E-ROD supports the collection and administration of professional drivers' data: personal details, journeys, activities, vehicle allocations, vehicles, and annotations. This repository contains the global administration system, client administration system, and the PHP API used by the driver app.

This is an existing production project. This documentation was refreshed on 2026-09-29 after the local files were restored, using repository-wide file inventory and Git comparisons, targeted source inspection, and the user-supplied database column export. It does not independently verify the deployed version, live database, or runtime behavior. No application requests, database operations, or email deliveries were performed for this review.

## Demo display setting

Set `<appdemo>true</appdemo>` directly under `<core>` in the active XML configuration to display all five demo banners. Set it to `false` to omit their complete HTML blocks. Missing or unrecognized values default to hidden; `true` is matched without case sensitivity and surrounding whitespace is ignored. This demo checkout has it enabled in both the tracked template and ignored local configuration.

`config.local.xml` takes precedence over `config.xml` as a whole, so change the local file when it exists. `Core::is_demo()` reads the already-loaded configuration without another file read or database query. The setting controls only the admin login, global admin header, client admin header, public home/login banner, and driver information banner; it does not select a database or change API authentication.

Before deploying to production, set `<appdemo>false</appdemo>` in its local configuration. The five templates can then be shared across branches. The driver block now also omits its banner in production, correcting the historical unconditional banner present in both branches.

## Historical branch baseline (2026-09-29)

The inspected checkout is on `demo` at `5f87596`; local `main` is at `b6938fe`. All 168 tracked files are present. The application files match the committed demo branch except for the existing activity synchronization edit described below. Assets and bundled libraries were included in the Git comparison; this was not a fresh line-by-line audit of third-party code or binary assets. Remote branches were not fetched, so this review cannot establish whether a newer version exists upstream or on production.

At that baseline there was **no explicit demo/production mode flag**; the new display setting above supersedes the hardcoded banner behavior. Configuration contains `dbconnection` fields (`servername`, `username`, `password`, `dbname`) and, since the 2026-10-15 configuration refactor, `apikeys.web` and `apikeys.app`. The branch comparison below describes the earlier committed baseline. The two local branch commits differ in five files:

| Files | Difference on `demo` relative to `main` |
| --- | --- |
| `app/config/config.xml` | Different `dbname` value |
| `login.php`, `app/design/adminhtml/userinfo.phtml`, `app/design/publichtml/userinfo.phtml`, `app/design/publichtml/pages/home.phtml` | Hardcoded demonstration banners |

Business logic and API code are identical between those branch commits. The separate `app/design/publichtml/block/driverinfo.phtml` demo banner exists unconditionally in both branches. Branch names and banners do not enforce database isolation or disable writes/email.

The restored working tree has these relevant characteristics:

- Demo banners are present again. The earlier local changes to login handling, configuration, and routing are no longer present relative to the demo commit.
- `PublicOperations::sync_useractivities_list()` now resolves a missing journey ID using `$actitem[8]`, whereas both branch commits use `$actitem[13]`. Position 8 matches `sACTjrnstr` in the nine-field activity SELECT and insert/update code. This existing local correction was preserved; its runtime behavior has not been tested.
- `Core::decrypt_pwd()` again requires both the time code and numeric sequence to match. Admin JavaScript generates its time code in UTC; PHP compares it with `date("iwH")` in the active PHP timezone. Clock/timezone differences and minute boundaries need attention when diagnosing login failures.
- `.htaccess` again contains a host-specific HTTPS redirect for `geotarget.pt`. This is not an environment selector or a general HTTPS rule for the application's host.

The database reference removed during the restore has been recovered from the supplied export at [docs/database-schema.md](docs/database-schema.md).

## Preserving configuration during merges

`.gitattributes` assigns `merge=ours` to `app/config/config.xml`. Configure the custom driver in each clone:

```powershell
git config --local merge.ours.driver true
```

The driver leaves the current branch's file unchanged when Git invokes a content merge. It is not specific to `main`, and it does not protect against fast-forward merges or incoming-only file changes. The attribute must already be present on the receiving branch before relying on it; merely adding it on `demo` is not sufficient setup for `main`.

To preserve the exact pre-merge `main` configuration, start with a clean working tree and use this deliberate merge workflow:

```powershell
git switch main
git merge --no-ff --no-commit demo
# If a merge is in progress, restore the pre-merge main configuration:
git restore --source=HEAD --staged --worktree -- app/config/config.xml
git diff --cached --quiet -- app/config/config.xml
# Resolve other conflicts, review and validate the staged changes, then commit.
```

Only run the restore after confirming a merge is in progress; it discards staged/working changes to this one file. The quiet diff must exit with status 0 before committing. This workflow preserves the configuration file, including database settings and API keys; demo banner changes still need review. No branch switch, merge, or commit was performed when adding this rule.

## Architecture

The application is a custom PHP application using MySQL through MySQLi. It uses explicit PHP includes, server-rendered PHP/PHTML templates, and browser JavaScript. No framework bootstrap, Composer/npm manifest, schema migrations, or database dump was found in the initial inspection. A standalone authentication regression test now lives in `tests/apprequestauth_test.php`. Libraries and browser assets are bundled directly.

| Module | Entry and routing | Main implementation |
| --- | --- | --- |
| Global administration | `/admin` -> `index.php?role=admin` -> `backend.php` -> `system.php` | `AdminInfo`, `AdminLists`, `AdminData`, `AdminOperations`; `app/design/adminhtml/`; `skin/adminhtml/` |
| Client administration | `/` or `/public` -> `frontend.php` -> `pubadmin.php` for public user types <= 1 | `PublicInfo`, `PublicLists`, `PublicData`; `pubadmin*.php` processing scripts also reuse `AdminOperations`; `app/design/publichtml/`; `skin/publichtml/` |
| Driver app API | Direct URLs under `/app/code/processing/` | `appauthapi.php`, `appusersapi.php`, `appreportapi.php`, and shared public data/list/operation classes |

The `public` role is the customer-facing side, including authenticated client administration; it does not mean all its pages are anonymous. `frontend.php` renders `site.php` for unauthenticated visitors and `app.php` for authenticated user types > 1. The latter currently renders an app redirect on its home page. `app.php` is a web view, not the mobile API entry point. The mobile application source was not found in this checkout.

### Web request flow

1. Apache `.htaccess` rewrites paths to `index.php` using `role`, `pg`, `id`, and optional `param` parameters.
2. `index.php` selects the global or public frontend; absent a role, it selects public.
3. The frontend initializes `Core`, reads credentials/cookies, and checks database-backed sessions.
4. Page templates include blocks and use data/list helpers to render content.
5. Browser form scripts send requests directly to `app/code/processing/`, whose handlers perform validation and database operations.

Global administration covers entities/companies, operating units, public users/drivers, vehicles, and plans. Client administration exposes company-related data and users, with blocks for units, vehicles, plans, journeys, and activities. Visibility in the UI should not be assumed to prove authorization in a processing endpoint.

## Repository map

| Path | Purpose |
| --- | --- |
| `index.php`, `.htaccess` | Role dispatch and Apache URL rewriting |
| `backend.php`, `system.php`, `login.php` | Global administrator bootstrap, page selection, and login |
| `frontend.php`, `pubadmin.php`, `site.php`, `app.php` | Public/client bootstrap and user-specific views |
| `app/code/base/core.php` | XML configuration, shared MySQLi connection, directory map, credentials and sessions |
| `app/code/base/base.php` | Shared domain queries and helpers in `BaseElements` |
| `app/code/base/admin*.php` | Global admin data, lists, presentation helpers, and operations |
| `app/code/base/public*.php` | Public/client/driver data, lists, presentation helpers, and synchronization |
| `app/code/processing/` | Directly callable form and API handlers |
| `app/code/base/functions/` | Validation and email composition/delivery helpers |
| `app/code/base/lib/` | Bundled PHPMailer implementation |
| `app/design/adminhtml/`, `app/design/publichtml/` | Pages, layout fragments, and reusable blocks |
| `skin/adminhtml/`, `skin/publichtml/` | JavaScript, CSS, images, fonts, and bundled browser libraries |
| `app/config/config.xml` | Database connection configuration; treat values as sensitive |
| `media/` | Referenced image/file resources |
| `_DEV_TMP_/` | Ignored local working material; not an authoritative setup or migration source |

Browser assets include Bootstrap, Chart.js, and Font Awesome; public views also reference swipe/lightbox libraries. No asset build step was identified.

## Driver API and synchronization

These are existing action-based PHP endpoints, not a versioned REST interface. The app endpoints decode a JSON request body from `php://input` and compare a string `reqcode` value against the configured `apikeys.app` value using `hash_equals`. Form POST handlers use `apikeys.web`. Missing or empty configured keys reject requests before any database initialization. Do not copy request codes or credentials into documentation, fixtures, or logs.

| Endpoint under `app/code/processing/` | Actions and behavior |
| --- | --- |
| `appauthapi.php` | `applogin`, `appsescheck`, `applogout`; uses `appusr`/`apppwd` or `appuser`/`appses`. Responses include `appuser`, `appsession`, `ttl`, `authstate`, `autherror`. Login checks driver type, company/user status, and subscription status; the session-allowance helper currently always permits another session. |
| `appusersapi.php` | `query` reads datasets; `compare` synchronizes supported datasets. Inputs include `userid`, `qid`, and `pref`. |
| `appreportapi.php` | `emlreport` composes and sends activity reports; inputs include `userid`, `rptType`, and `rptEmail`. This has an external email side effect. |
| `usersapi.php`, `reportapi.php` | Related web handlers read form POST fields rather than the app endpoints' JSON envelope. |
| `admin*.php`, `pubadmin*.php` | Administration form handlers for entities, units, users, vehicles, and, on the global side, plans. |

`appusersapi.php` dataset selection:

| `qid` | Dataset | `query` | `compare` |
| --- | --- | --- | --- |
| 0 | Driver details | Yes | No |
| 1 | Company details | Yes | No |
| 2 | Operating unit, with company fallback | Yes | No |
| 3 | Vehicles | Yes | Yes |
| 4 | Journeys | Yes | Yes |
| 5 | Vehicle allocations | Yes | Yes |
| 6 | Activities | Yes | Yes |
| 7 | Annotations/remarks | Yes | Yes |

For `compare`, `pref` is a JSON-encoded string that `PublicOperations` decodes again into positional record arrays. Synchronization may insert records, close/update existing records, resolve temporary journey/vehicle references, and mark remarks deleted before returning updated lists. **`compare` is a write operation.** Array positions, sentinel records, state values, identifier mappings, and date formats are part of the existing app contract. A complete field-by-field protocol specification still needs to be established against the mobile client.

Responses commonly contain positional arrays; rejected request codes return plain text. Do not assume consistent JSON errors or HTTP status handling. Versioned app requests now validate user/session ownership before reaching data or report operations. Legacy requests retain the API-key-only path until the migration deadline is defined; browser handlers and record-level ownership still require a separate authorization review.

### Legacy and versioned app authentication

The three `app*api.php` endpoints still accept JSON request bodies, not PHP form `$_POST` fields. All requests require the configured app API key in `reqcode`.

| Request | Additional authentication |
| --- | --- |
| `appversion` absent, empty string, or whitespace-only string | Legacy behavior is unchanged |
| Nonempty string or numeric `appversion` on data/report requests | Positive `userid` and `appses` matching an unexpired public session |
| Versioned `applogin` | Existing credential, company, user, and subscription checks; no prior session required |
| Versioned `appsescheck` or `applogout` | Positive `appuser` and matching unexpired `appses`, then the existing action |

`appversion` is an opt-in marker, not a minimum-version comparison. Numeric zero and the string `0` also opt in. Null, boolean, array, and object versions are rejected rather than falling back to legacy. The existing lowercase 32-character session token is returned as `appsession` at login and sent back unchanged as `appses`; no additional hashing is required.

`AppRequestAuth` validates the key and request fields before the session callback can initialize the database. `Core::has_public_session()` performs one prepared, read-only lookup on `publicsessions`, binding the user ID and token and checking positive expiry plus server-side expiration. Data/report requests do not renew expiry. Versioned session-check/logout actions perform this guard before their existing queries; `appsescheck` still renews expiry and checks company/user/subscription status.

A rejected versioned guard returns HTTP 401 and JSON with `authstate: false` and `autherror`. The auth endpoint also includes `appuser: 0`, `appsession: null`, and `ttl: 0`. Legacy key failures retain the existing plain-text response. Login credential failures retain the existing login response. Successful endpoint response shapes are unchanged.

No deadline has been configured yet. Omitting the version still selects legacy authentication, so session enforcement is not universal until that fallback is removed. Login remains the exception when the migration ends. No new database indexes were created: index availability and production query performance require verification against a safe environment.

Run `php tests/apprequestauth_test.php` for isolated authentication tests. They use synthetic inputs, callback/connection doubles, and no database or application requests. They cover legacy compatibility, malformed values, mismatched identities/tokens, session lookup results, login exemption, auth actions, parameter binding, and error responses. They do not verify a real SQL execution plan or end-to-end Android behavior.

### API key configuration

**Sanitized checkpoint (2026-10-15):** tracked `config.xml` contains empty database/API-key fields and a non-secret `appdemo` display setting. Copy it to ignored `app/config/config.local.xml` and supply the environment-specific database settings and both keys there. `Core::config_path()` prefers that local file for database and API configuration, falling back to `config.xml` only when no local file exists. The local file fully replaces the tracked configuration; it is not a field-by-field overlay. Existing local values were preserved without rotation. Deployments must supply their own local file before running this version. Do not commit or overwrite that file during deployment. This does not remove secrets already present in Git history.


`app/config/config.xml` contains an `apikeys` element alongside `dbconnection`, with separate `web` and `app` child elements. All 14 processing endpoints read the relevant value through `Core::api_key()` without initializing a database connection. Existing key values were moved unchanged; no keys were rotated and no production branch was edited.

Browser pages expose only the web key in an HTML meta tag, and the browser scripts read that value when sending `reqcode`. The web key is therefore visible to browser users and is not a substitute for session or company authorization. The app key is not rendered into the pages; mobile clients must send the value configured for their target environment.

Populate both keys independently in demo and production before deploying this code. When first merging into `main`, preserve its database settings and add the new `apikeys` fields using the existing production client keys. Restoring an older configuration without those fields will cause all processing requests to be rejected. Different environment keys require matching clients; this refactor does not automatically generate or distribute new keys.

`app/config/.htaccess` denies HTTP access to configuration files on Apache 2.4 when overrides permit authorization directives. Verify this protection in the deployment configuration; PHP can still read the file locally. Credentials and keys belong in the ignored local XML file; the tracked XML is sanitized. The `merge=ours` limitations and deliberate merge workflow above apply to keys as well as database parameters.

## Data model and authentication

The supplied database export lists 19 tables. Their column names, order, and SQL types are recorded in [Database structure reference](docs/database-schema.md). The roles below are based on application SQL and match the supplied table inventory:

| Tables | Role |
| --- | --- |
| `users`, `usrgroups`, `sessions` | Global administrators, groups, and sessions |
| `publicusers`, `publicsessions` | Client users/drivers and their sessions |
| `entities`, `opunits` | Companies and operating units |
| `plans`, `plan_type` | Plans and subscription-related settings |
| `vehicles`, `vehbrands`, `vehalloc` | Vehicles, brands, and journey vehicle allocations |
| `journeys`, `activities`, `userremarks` | Driver journeys, activity records, and annotations |
| `entity_type`, `segments`, `nuts`, `zones` | Supporting classifications and geographic data |

Company/user/journey relationships are expressed in query fields and application logic. Column types are now documented from the supplied export; foreign keys, indexes, nullability, defaults, and other database constraints remain unknown. Several entities use deletion flags rather than physical removal. The reference is not an executable schema or a substitute for a complete structure export.

Web authentication uses `adminactivesession` and `publicactivesession` cookies with database session records. App authentication shares the public session implementation. Cookie/API expiry values and database expiry logic are separate and must be inspected together. Password verification currently uses SHA-256, with a custom password transformation in the login flow; session tokens are generated using MD5. These are existing compatibility details, not recommended patterns for new authentication code.

Both session-allowance helpers currently bypass their computed restriction: `user_session_allowed()` returns `true`, and `public_user_session_allowed()` sets `$allow = true` before returning. Multiple logins are therefore permitted by these helpers, despite single-session error messages remaining in callers. This does not disable session validity/expiry checks.

## Local setup prerequisites

There is no verified one-command installation process. Before running the application:

1. Obtain the deployed PHP and MySQL/MariaDB versions, SQL mode, complete schema DDL, and required lookup data from the maintainer. The supplied [column inventory](docs/database-schema.md) is available for reference but lacks the constraints and settings needed to recreate the database. The supported version range is not declared in this checkout.
2. Prepare an isolated database with sanitized data. Do not use production credentials or a production database for development.
3. Configure an Apache virtual host with this repository as its document root, PHP enabled, `mod_rewrite`, and overrides sufficient for `.htaccess`. Paths such as `/skin/...` and `/app/code/processing/...` assume hosting at the web root.
4. Enable PHP MySQLi and SimpleXML. JSON support is also used. Confirm the complete extension requirements against the chosen runtime and bundled libraries.
5. Copy the sanitized `app/config/config.xml` to ignored `app/config/config.local.xml` and supply local values there under `dbconnection`: `servername`, `username`, `password`, and `dbname`. There is no environment-variable loader or sanitized configuration template identified here. Do not commit credentials.
6. Review `app/code/base/functions/sendmail.php` before exercising any report workflow. It contains SMTP settings and credentials in source; local testing needs an isolated mail sink or disabled delivery.
7. Align PHP and database time settings. Processing scripts explicitly use `Europe/Lisbon`, while SQL also uses `NOW()` and UNIX timestamps. `Core` currently sets the connection charset to `utf8`; confirm the schema charset before changing it.

`Core::init()` opens a database connection immediately, and session checks can update session rows. Even loading a page is not necessarily read-only. PHP's built-in server does not automatically reproduce the Apache rewrite configuration.

## Verification for future changes

### Web login diagnostics (2026-10-16)

Admin and public-site JavaScript encode a UTC time code. Both `backend.php` and `frontend.php` call `decrypt_pwd(..., true)` to validate UTC with a one-minute allowance either side, independent of the PHP timezone. Numeric-sequence validation remains enforced. A decoding rejection produces a request/clock-validation message and does not attempt a credential lookup. The standard invalid-credentials message remains for database credential rejections. Both button handlers cancel their default submission before submitting the encoded form.

The decoder's default behavior, used by the mobile app, is unchanged. Run `node tests/admin_login_test.js` for the admin encoder and `node tests/admin_login_test.js --public` for the public-site encoder against the PHP decoder without database access. These checks verify UTC/timezone handling and stale/malformed rejection, not real user credentials or deployed login success.

Start with `git status --short` and preserve unrelated work. PHP syntax checks can be run without executing the application, using the target runtime, for example:

```powershell
php -l app/code/base/core.php
php -l app/code/processing/appusersapi.php
git diff --check
```

Lint each changed PHP/PHTML file; syntax success does not verify database compatibility or behavior. Once an isolated environment exists, check the affected workflow and its counterpart in the other admin module. Relevant scenarios include login/logout and expiry, cross-company access denial, vehicle/user edits, sync replay without duplicates, journey closure, temporary ID resolution, annotations, and report delivery to a mail sink. No runtime or integration checks were performed for this documentation baseline.

## Initial review findings and next work

These are source observations and follow-up priorities, not a completed security audit:

- **Authorization:** request-code checks and supplied record/user IDs appear in direct processing handlers. Trace session, role, and company ownership checks for every read/write path; do not rely on frontend visibility or the shared request code.
- **SQL and output handling:** shared classes interpolate values into SQL. Review parameter binding, validation, template escaping, and CSRF protection across the two admin modules.
- **Secrets and authentication:** inventory XML API keys, SMTP credentials, XML database credentials, password hashing, session generation, and cookie flags. Plan any changes with deployed app compatibility in mind.
- **API stability:** document positional payloads, duplicate detection, time handling, synchronization order, and failure responses before changing synchronization behavior.
- **Routing gaps:** `pubadmin.php` references `publicpagess` for configuration, while `Core::tree_struct()` defines `publicpages`. Both admin page selectors reference configuration templates absent from the inspected page directories. These paths need verification before being advertised as working features.
- **Operational baseline:** establish the schema/migration source, runtime versions, deployment and rollback process, backups, dependency versions, and a safe test environment.

Agent working guidance is in [AGENTS.md](AGENTS.md). The current `.gitignore` excludes that file, so it is local guidance unless deliberately added to version control; this documentation task leaves that rule unchanged.

# RestCord – Agent Guidelines

## Repository Overview

RestCord is a PHP 8.1+ library for the Discord REST API v10. It wraps
[GuzzleHTTP](https://github.com/guzzle/guzzle) with a service-description
approach: all endpoints are declared in
`src/Resources/service_description-v10.json` and exposed as typed PHP
interfaces under `src/Interfaces/`.

Key directories:

| Path | Purpose |
|---|---|
| `src/` | Library source (PSR-4 namespace `RestCord\`) |
| `src/Interfaces/` | One interface per Discord API resource group |
| `src/Model/` | Value-object models returned by API calls |
| `src/Resources/` | `service_description-v*.json` + Twig templates for code generation |
| `src/RateLimit/` | Rate-limit middleware and providers |
| `tests/` | PHPUnit test suite (PSR-4 namespace `RestCord\Test\`) |
| `bin/` | Helper scripts (`lint`, `buildDocs`, `buildModelClasses`, etc.) |
| `extra/` | Miscellaneous resources (e.g. `gateway.html`) |
| `docs/` | Jekyll-based documentation site source |

---

## Environment Setup

```bash
# Install runtime and dev dependencies
composer install
```

Requires PHP ≥ 8.1.

---

## Lint, Build, and Test

Run these commands from the repository root.

### Lint (PHP syntax check)

```bash
./bin/lint src
```

### Unit tests

```bash
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests
```

Both commands are executed by CI (GitHub Actions and CircleCI) on PHP 8.1,
8.2, 8.3, 8.4, and 8.5.

### Code-generation helpers (optional)

The `bin/` scripts regenerate model classes, interface stubs, and documentation
from `service_description-v10.json`. Run them only when updating the service
description or adding new Discord API endpoints:

```bash
./bin/buildModelClasses
./bin/buildDummyClasses
./bin/buildDocs
```

---

## Coding Conventions

* **PHP 8.1+** features are allowed (enums, fibers, readonly properties,
  intersection types, etc.).
* Namespace root: `RestCord\`. Tests live under `RestCord\Test\`.
* Match the existing docblock style: `@param`, `@return`, `@throws` tags with
  types.
* No strict `declare(strict_types=1)` is enforced project-wide; follow the
  style of the file being edited.
* Discord snowflake IDs are typed as `integer` inside the service descriptions
  and cast accordingly in `DiscordClient::updateParameterTypes()`.
* When adding a new Discord API operation:
  1. Add the entry to `src/Resources/service_description-v10.json`.
  2. Re-run `bin/buildModelClasses` and `bin/buildDummyClasses` if new models
     or interface methods are needed.
  3. Add or update the corresponding test(s) in `tests/`.

---

## Testing Guidelines

* All tests live in the `tests/` directory and extend PHPUnit's
  `TestCase`.
* Test file names follow the pattern `<ClassName>Test.php`.
* Run the full suite before opening a PR:

  ```bash
  ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests
  ```

* Do **not** remove or skip existing tests. If behaviour changes, update the
  assertions rather than deleting the test.
* Tests that exercise the service description (e.g. `ServiceDescriptionV10Test`)
  are integration-style: they load the real JSON files and validate structure.

---

## Pull Request Guidelines

* Keep changes focused and minimal.
* Ensure `./bin/lint src` and the PHPUnit suite both pass before requesting
  review.
* Update `DISCORD_API_V10_COMPATIBILITY.md` when adding or changing API v10
  operations.
* The library targets Discord API **v10**; do not add v6/v9-only features.

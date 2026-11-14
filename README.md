# Notification Service

Laravel API for the WhatsApp notification service.

## CI/CD

The GitLab pipeline runs on a project runner with the Docker executor:

| Stage | What it does |
|---|---|
| validate | `composer validate` |
| test | installs dependencies and runs the Laravel tests on in-memory SQLite |
| build | builds two images from the `Dockerfile` (`app` = PHP-FPM, `web` = nginx) and pushes them to the GitLab container registry, tagged with the commit SHA |
| deploy | on the default branch only: starts the Compose test stack, runs migrations, checks `/api/health` |

The test stack publishes nginx on the private address `172.31.34.196:8081`
only. It is not exposed to the public internet.

Deployment secrets are GitLab CI/CD variables, masked, protected and scoped
to the `test` environment: `TEST_APP_KEY` and `TEST_DB_PASSWORD`. Nothing
secret is stored in the repository or on the runner host.

Rollback: GitLab > Operate > Environments > `test` > re-deploy an earlier
deployment. That re-runs the deploy job of the older commit, which starts the
images tagged with that commit. Migrations are not reversed by a rollback.

The test deployment does not seed the database with the sample administrator
account in `DatabaseSeeder`.

## Local test stack

Copy `.env.example` to `.env`, set a unique `DB_PASSWORD` and a Laravel
`APP_KEY`, then run:

```sh
docker compose up -d --build --wait
docker compose exec app php artisan migrate --force
curl http://127.0.0.1:8081/api/health
```

The API health endpoint does not require the WhatsApp provider credentials.
Set those as deployment secrets only when testing actual message delivery.

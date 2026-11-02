# Notification Service

Laravel API for the WhatsApp notification service.

## CI/CD

The GitLab pipeline validates Composer metadata, installs dependencies and runs
the Laravel tests, builds a commit-tagged Docker image, then deploys the default
branch to the GitLab VM for a local test. The test stack includes PostgreSQL
and publishes Nginx only on `127.0.0.1:8081`; it is not exposed directly to the
public internet.

The deployment job expects a project-scoped shell runner on the GitLab VM with
the `dev` and `notification-shell` tags. It uses the host Docker Engine and
Docker Compose. The deploy environment file belongs on that VM at
`/etc/notification-service/test.env`, readable only by root and the runner.
Create it outside the repository with these values:

```dotenv
APP_KEY=base64:<a fresh Laravel key>
DB_PASSWORD=<a unique random password>
```

Do not commit that file. The test deployment does not seed the database with
the sample administrator account in `DatabaseSeeder`.

## Local test stack

Copy `.env.example` to `.env`, set a unique `DB_PASSWORD` and a Laravel
`APP_KEY`, then run:

```sh
docker compose up -d --build
docker compose exec app php artisan migrate --force
curl http://127.0.0.1:8081/api/health
```

The API health endpoint does not require the WhatsApp provider credentials.
Set those as deployment secrets only when testing actual message delivery.

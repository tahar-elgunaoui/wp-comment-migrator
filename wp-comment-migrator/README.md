WordPress Comment Migrator

Simple Docker + Artisan tool to migrate WordPress comments.

Requirements

Docker
Docker Compose

Setup

docker compose up -d
docker compose exec app composer install

Run migration

docker compose exec app php artisan comments:migrate

Features

- remaps post IDs
- remaps user IDs
- keeps guest comments without user id
- updates WordPress comment counters automatically

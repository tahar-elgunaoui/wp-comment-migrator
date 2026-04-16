
# WordPress Comment Migrator

Portable tool to migrate **WordPress comments** from a **production database (prod)** to another **WordPress database (vdev/dev)** while:

- remapping post IDs
- remapping user IDs
- keeping guest comments intact
- automatically fixing WordPress comment counters

The tool runs inside **Docker** and is executed using **Laravel Artisan commands**.

---

# Requirements

You must have installed:

- Docker
- Docker Compose
- Access to both WordPress databases

Test installation:

```
docker --version
docker compose version
```

---

# Project Structure

```
wp-comment-migrator
│
├─ docker
│  └─ php
│     └─ Dockerfile
│
├─ docker-compose.yml
├─ composer.json
├─ artisan
├─ README.md
│
├─ app
│  └─ Console
│     └─ Commands
│        └─ MigrateComments.php
│
└─ config
   └─ database.php
```

---

# Setup

Go inside the project folder:

```
cd wp-comment-migrator
```

Start the Docker container:

```
docker compose up -d
```

Install PHP dependencies:

```
docker compose exec app composer install
```

---

# Configure Database Connections

Edit the file:

```
config/database.php
```

Example configuration:

```
'prod' => [
    'driver' => 'mysql',
    'host' => 'prod-db-host',
    'database' => 'prod_database',
    'username' => 'prod_user',
    'password' => 'prod_password'
],

'vdev' => [
    'driver' => 'mysql',
    'host' => 'dev-db-host',
    'database' => 'vdev_database',
    'username' => 'dev_user',
    'password' => 'dev_password'
]
```

Make sure the container can reach both databases.

---

# Configure Post ID Mapping

Open:

```
app/Console/Commands/MigrateComments.php
```

Edit the `$postMap` array.

Example:

```
$postMap = [
401 => 1321,
446 => 1421,
389 => 1302,
411 => 1337
];
```

Format:

```
OLD_POST_ID => NEW_POST_ID
```

---

# Configure User ID Mapping

Inside the same file configure:

```
$userMap = [
35 => 48,
12 => 3,
22 => 2,
8 => 7
];
```

Format:

```
OLD_USER_ID => NEW_USER_ID
```

---

# Default User

If a comment belongs to a user not present in the map, it will be assigned to the default user:

```
$defaultUser = 5;
```

Guest comments remain with:

```
user_id = 0
```

---

# Run Migration

Execute:

```
docker compose exec app php artisan comments:migrate
```

The script will:

1. Read comments from the production database
2. Replace post IDs using the map
3. Replace user IDs using the map
4. Keep guest comments untouched
5. Insert comments into the vdev database

---

# Fix WordPress Comment Counters

After inserting comments the script automatically runs:

```
UPDATE wp_posts p
SET comment_count = (
    SELECT COUNT(*)
    FROM wp_comments c
    WHERE c.comment_post_ID = p.ID
    AND c.comment_approved = '1'
);
```

This ensures the comment count displayed in WordPress is correct.

---

# Verify Migration

After running the script:

Check in WordPress admin:

```
Posts → Comments
```

Or run:

```
SELECT COUNT(*) FROM wp_comments;
```

---

# Troubleshooting

### Cannot connect to database

Check:

```
config/database.php
```

Ensure:

- host
- username
- password
- database

are correct.

---

### Docker container not running

Restart container:

```
docker compose down
docker compose up -d
```

---

### Composer not installed

Run again:

```
docker compose exec app composer install
```

---

# Safety Recommendation

Before running the migration:

Backup the vdev database.

Example:

```
mysqldump -u root -p vdev_database > backup.sql
```

---

# Notes

The script **does not modify the production database**.

It only:

- reads comments from prod
- writes them to vdev

---

# License

Internal tool for WordPress migrations.

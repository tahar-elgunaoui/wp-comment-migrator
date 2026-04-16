
# WordPress Comment Migrator

Simple tool to migrate **WordPress comments** from a **production database** to a **development database**.

Features:

- migrate comments
- map old post IDs to new post IDs
- map user IDs
- keep guest comments
- fix WordPress comment counters automatically

---

# Requirements

- Docker

---

# Setup

Start Docker:

```
docker compose up -d
```

Install dependencies:

```
docker compose exec app composer install
```

---

# Configure Databases

Edit:

```
config/database.php
```

Set your **production** and **development** database credentials.

---

# Configure ID Mapping

Edit:

```
app/Console/Commands/MigrateComments.php
```

### Post IDs

```
$postMap = [
401 => 1321,
446 => 1421
];
```

Format:

```
OLD_POST_ID => NEW_POST_ID
```

### User IDs

```
$userMap = [
35 => 48,
12 => 3
];
```

Format:

```
OLD_USER_ID => NEW_USER_ID
```

Default user:

```
$defaultUser = 5;
```

Guest comments keep:

```
user_id = 0
```

---

# Run Migration

```
docker compose exec app php artisan comments:migrate
```

The script will:

1. read comments from production
2. map post IDs
3. map user IDs
4. insert comments into dev database
5. fix WordPress comment counters

---

# Comment Counter Fix !!!!!!!!

The script runs automatically:

```
UPDATE wp_posts p
SET comment_count = (
  SELECT COUNT(*)
  FROM wp_comments c
  WHERE c.comment_post_ID = p.ID
  AND c.comment_approved = '1'
);
```

---

# Safety

Always backup your dev database before running:

```
mysqldump -u root -p vdev_database > backup.sql
```

---

# Done

After running the command, check:

```
WordPress Admin → Comments
```

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Capsule\Manager as DB;

class MigrateComments extends Command
{
    protected $signature = 'comments:migrate';

    protected $description = 'Migrate WordPress comments from prod to vdev';

    public function handle()
    {

        $postMap = [
            401 => 1321,
            446 => 1421,
            389 => 1302,
            411 => 1337,
            423 => 1359
        ];

        $userMap = [
            35 => 48,
            12 => 3,
            22 => 2,
            8 => 7,
            34 => 6,
            10 => 8,
            11 => 9
        ];

        $defaultUser = 5;

        $comments = DB::connection('prod')
            ->table('wp_comments')
            ->whereIn('comment_post_ID', array_keys($postMap))
            ->get();

        foreach ($comments as $comment) {

            $comment->comment_post_ID = $postMap[$comment->comment_post_ID];

            if ($comment->user_id > 0) {
                $comment->user_id = $userMap[$comment->user_id] ?? $defaultUser;
            } else {
                $comment->user_id = 0;
            }

            DB::connection('vdev')
                ->table('wp_comments')
                ->insert((array) $comment);
        }

        DB::connection('vdev')->statement("
            UPDATE wp_posts p
            SET comment_count = (
                SELECT COUNT(*)
                FROM wp_comments c
                WHERE c.comment_post_ID = p.ID
                AND c.comment_approved = '1'
            )
        ");

        $this->info('Comments migrated successfully');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                if (Schema::hasColumn('news', 'image_path')) {
                    $table->text('image_path')->nullable()->change();
                }
                if (Schema::hasColumn('news', 'file_news')) {
                    $table->text('file_news')->nullable()->change();
                }
            });

            // Convert existing string values to JSON arrays if not already
            DB::table('news')->select('news_id', 'image_path', 'file_news')->orderBy('news_id')->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $imagePath = $row->image_path;
                    $fileNews = $row->file_news;
                    $update = [];
                    if ($imagePath && $this->isJson($imagePath) === false) {
                        $update['image_path'] = json_encode([$imagePath]);
                    }
                    if ($fileNews && $this->isJson($fileNews) === false) {
                        $update['file_news'] = json_encode([$fileNews]);
                    }
                    if (!empty($update)) {
                        DB::table('news')->where('news_id', $row->news_id)->update($update);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                if (Schema::hasColumn('news', 'image_path')) {
                    $table->string('image_path', 255)->nullable()->change();
                }
                if (Schema::hasColumn('news', 'file_news')) {
                    $table->string('file_news', 255)->nullable()->change();
                }
            });

            // Convert JSON arrays back to first element string (best effort)
            DB::table('news')->select('news_id', 'image_path', 'file_news')->orderBy('news_id')->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $update = [];
                    if ($row->image_path && $this->isJson($row->image_path)) {
                        $arr = json_decode($row->image_path, true);
                        $update['image_path'] = is_array($arr) && count($arr) ? $arr[0] : null;
                    }
                    if ($row->file_news && $this->isJson($row->file_news)) {
                        $arr = json_decode($row->file_news, true);
                        $update['file_news'] = is_array($arr) && count($arr) ? $arr[0] : null;
                    }
                    if (!empty($update)) {
                        DB::table('news')->where('news_id', $row->news_id)->update($update);
                    }
                }
            });
        }
    }

    private function isJson(string $value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE && (is_array(json_decode($value, true)) || is_object(json_decode($value))); 
    }
};

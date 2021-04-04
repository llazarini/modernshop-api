<?php
namespace App\Models;

use Illuminate\Support\Facades\Log;

trait FileTrait
{
    public static function boot()
    {
        parent::boot();
        static::created(function($record) {
            $request = request();
            Log::debug(self::class);
            Log::debug($request->get('request_token'));
            Log::debug($request->get('request_token'));
            Log::debug($record->id);
            File::whereType(self::class)
                ->whereRequestToken($request->get('request_token'))
                ->update([
                    'type_id' => $record->id,
                ]);
        });
        static::updated(function($record) {
            $request = request();
            File::whereType(self::class)
                ->whereRequestToken($request->get('request_token'))
                ->update([
                    'type_id' => $record->id,
                ]);
        });
    }

    public function files() {
        return $this->morphMany(File::class, 'type');
    }

    public function file() {
        return $this->morphOne(File::class, 'type');
    }
}

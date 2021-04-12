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
        return $this->morphMany(File::class, 'type', 'type', 'type_id');
    }

    public function file() {
        return $this->morphOne(File::class, 'type');
    }
}

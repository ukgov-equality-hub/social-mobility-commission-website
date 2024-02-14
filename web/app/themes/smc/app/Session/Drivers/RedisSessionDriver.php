<?php

namespace App\Session\Drivers;

use Rareloop\Lumberjack\Facades\Log;


class RedisSessionDriver implements \SessionHandlerInterface
{
    private $savePath;

    private $redisConnection;

    private $sessionLifetime;

    public function __construct($redisConnection, $sessionLifetime = 120)
    {
        $this->redisConnection = $redisConnection;
    }


    public function close(): bool
    {
        return true;
    }

    public function destroy($id): bool
    {
        try {
            $this->redisConnection->delete("sess_{$id}");
        } catch (\Exception $e) {
            Log::error('Failed to destroy redis session');
        }

        //filebased handling as backup
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($max_lifetime): bool
    {
        return true;
    }

    public function open($savePath, $sessionName): bool
    {
        //filebased handling as backup
        $this->savePath = $savePath;
        if (!empty($this->savePath)
            && !mkdir($concurrentDirectory = $this->savePath, 0777)
            && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return true;
    }

    public function read($id)
    {
        try {
            $redisData = $this->redisConnection->get("sess_$id");
            if (!empty($redisData)) {
                return $redisData;
            }
        } catch (\Exception $e) {
            Log::error('Failed to read session data from Redis');
        }

        //filebased handling as backup
        $fileData = (string)@file_get_contents("$this->savePath/sess_$id");

        return $fileData;
    }

    public function write($id, $data): bool
    {
        try {
            $this->redisConnection->set("sess_$id", $data);
            // values expire after $sessionLifetime set in config/session.php
            $this->redisConnection->expire("sess_$id", $this->sessionLifetime);
        } catch (\Exception $e) {
            Log::error('Failed to create session in Redis');
        }


        return true;
    }


}

<?php

class DockerFactory
{
    public static function start()
    {
        exec(sprintf('docker-compose up -d'));
    }

    public static function stop()
    {
        exec(sprintf('docker-compose up -d'));
    }
}

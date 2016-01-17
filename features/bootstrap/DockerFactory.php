<?php

class DockerFactory
{
    public static function start($service)
    {
        exec(sprintf('docker-compose up -d %s ', $service));
    }
}

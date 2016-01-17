<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp;
use Gaufrette\Adapter\Local as LocalAdapter;
use Ssh\Sftp as SftpClient;

class ProxyFactory
{
    public static function create($name)
    {
        switch ($name) {
            case 'local':
                $adapter = new LocalAdapter('./tmp');
                break;
            case 'sftp':
                $configuration = new Ssh\Configuration('localhost', '2222');
                $authentication = new Ssh\Authentication\Password('gaufrette', 'gaufrette');

                $session = new Ssh\Session($configuration, $authentication);
                $client = new SftpClient($session);
                $adapter = new Sftp($client, '/home/gaufrette/share/./', true);
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown adapter %s', $name));
        }

        return new Filesystem($adapter);
    }
}

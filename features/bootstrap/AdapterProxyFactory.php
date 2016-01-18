<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp;
use Gaufrette\Adapter\Local as LocalAdapter;
use Ssh\Sftp as SftpClient;

class AdapterProxyFactory
{
    /**
     * @param $name
     * @return Filesystem
     */
    public static function create($name)
    {
        switch ($name) {
            case 'local':
                $adapter = new LocalAdapter('./tmp');
                break;
            case 'sftp':
                $sftp = new \phpseclib\Net\SFTP('localhost', 2222);
                $sftp->login('gaufrette', 'gaufrette');

                $adapter = new \Gaufrette\Adapter\PhpseclibSftp($sftp, 'share', true);
                break;
            case 's3':
                $service = new \Aws\S3\S3Client(array('key' => 'your_key_here', 'secret' => 'your_secret', 'endpoint' => 'http://localhost:4569','bucket_endpoint' => true, 'region' => 'us-west-2', 'version' => '2006-03-01' ));
                $adapter  = new \Gaufrette\Adapter\AwsS3($service,'your-bucket-name');
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown adapter %s', $name));
        }

        return new Filesystem($adapter);
    }
}

<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp;
use Gaufrette\Adapter\Local as LocalAdapter;
use Ssh\Sftp as SftpClient;

class AdapterProxyFactory
{
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param $name
     * @return Filesystem
     */
    public function create($name)
    {
        switch ($name) {
            case 'local':
                $adapter = new LocalAdapter($this->getParameter('local', 'folder'), $this->getParameter('local', 'create'));
                break;
            case 'sftp_phpseclib':
                $sftp = new \phpseclib\Net\SFTP($this->getParameter('sftp', 'host'), $this->getParameter('sftp', 'port'));
                $sftp->login($this->getParameter('sftp', 'login'), $this->getParameter('sftp', 'password'));

                $adapter = new \Gaufrette\Adapter\PhpseclibSftp($sftp, $this->getParameter('sftp', 'folder'), true);
                break;
            case 'sftp':
                $configuration  = new Ssh\Configuration($this->getParameter('sftp', 'host'), $this->getParameter('sftp', 'port'));
                $authentication = new Ssh\Authentication\Password($this->getParameter('sftp', 'login'), $this->getParameter('sftp', 'password')); // for other options, check php-ssh docs

                $session   = new Ssh\Session($configuration, $authentication);
                $adapter   = new Gaufrette\Adapter\Sftp($session->getSftp());

                break;
            case 's3':
                $service = new \Aws\S3\S3Client(array(
                    'credentials' => ['key' => $this->getParameter('s3', 'key'),
                    'secret' => $this->getParameter('s3', 'secret'),
                    ],
                    'endpoint' => $this->getParameter('s3', 'endpoint'),
                    'bucket_endpoint' => $this->getParameter('s3', 'bucket_endpoint'),
                    'region' => $this->getParameter('s3', 'region'),
                    'version' => $this->getParameter('s3', 'version'),
                ));
                $adapter = new \Gaufrette\Adapter\AwsS3($service, $this->getParameter('s3', 'bucket'));
                break;
            case 'ftp':
                $adapter = new \Gaufrette\Adapter\Ftp('/', $this->getParameter('ftp', 'host'), array(
                    'username' => $this->getParameter('ftp', 'username'),
                    'password' => $this->getParameter('ftp', 'password'),
                    'passive' => $this->getParameter('ftp', 'passive'),
                    'port' => $this->getParameter('ftp', 'port'),
                ));
                break;

            case 'gridfs':
                $client = new MongoClient(sprintf('mongodb://%s:%s', $this->getParameter('gridfs', 'host'), $this->getParameter('gridfs', 'port')));
                $db = $client->selectDB($this->getParameter('gridfs', 'db'));

                $adapter = new \Gaufrette\Adapter\GridFS(new MongoGridFS($db));
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown adapter %s', $name));
        }

        return new Filesystem($adapter);
    }

    private function getParameter($key, $value)
    {
        return $this->options[$key][$value];
    }
}

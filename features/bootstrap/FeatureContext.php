<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;

class FeatureContext implements Context, SnippetAcceptingContext
{
    public function __construct(array $parameters){

        $this->factory = new AdapterProxyFactory($parameters);
    }
    /**
     * @var \Gaufrette\Filesystem
     */
    private $filesystem;

    /**
     * @var string|null
     */
    private $currentFileContent;

    /**
     * @var string|null
     */
    private $methodOutput;

    /**
     * @AfterScenario
     */
    public function cleanup()
    {
        $filesystems = array($this->factory->create('s3'), $this->factory->create('local'));
        foreach ($filesystems as $filesystem) {
            try {
                foreach ($filesystem->keys() as $file) {
                    if ($file === '.gitkeep') {
                        continue;
                    }
                    if ($filesystem->has($file)) {
                        $filesystem->delete($file);
                    }
                }
            }catch(\Exception $e){

            }
        }
    }

    /**
     * @Given I use :storage with adapter :adapter
     */
    public function iUse($storage, $adapter)
    {
         $this->filesystem = $this->factory->create($adapter);
    }

    /**
     * @Given file :name exists
     * @Given dir :name exists
     * @Given file :name exists with content:
     * @Given I write :name with content:
     */
    public function fileExistsWithContent($name, PyStringNode $string = null)
    {
        if (null === $string) {
            $this->filesystem->createFile($name);
        } else {
            $this->filesystem->write($name, $string->__toString(), true);
        }
    }

    /**
     * @When I read file :name
     */
    public function iReadFile($name)
    {
        $this->currentFileContent = $this->filesystem->read($name);
    }

    /**
     * @When I rename :source to :dest
     */
    public function iRename($source, $dest)
    {
        $this->filesystem->rename($source, $dest);
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $string)
    {
        if (null === $this->currentFileContent) {
            throw new \RuntimeException('No file was read yet');
        }

        return strstr($this->currentFileContent, $string->__toString());
    }

    /**
     * @When I call method :method of current fs adapter
     */
    public function iCallMethodOfCurrentAdapter($method)
    {
        $method = new \ReflectionMethod($this->filesystem->getAdapter(), $method);
        $this->methodOutput = $method->invoke($this->filesystem->getAdapter());
    }

    /**
     * @Then I should see :fileKey in :key
     */
    public function iShouldSeeIn($fileKey, $key)
    {
        return in_array($fileKey, $this->filesystem->listKeys());
    }

    /**
     * @Then I should not see :fileKey in :key
     */
    public function iShouldNotSeeIn($fileKey, $key)
    {
        return !($this->iShouldSeeIn($fileKey, $key));
    }

    /**
     * @When I delete file :key
     */
    public function iDeleteFile($key)
    {
        $this->filesystem->delete($key);
    }
}

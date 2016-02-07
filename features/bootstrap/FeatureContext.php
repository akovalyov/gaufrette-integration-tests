<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;

class FeatureContext implements Context, SnippetAcceptingContext
{
    public function __construct(array $parameters)
    {
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
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @AfterScenario
     */
    public function resetHead()
    {
        exec('cd vendor/knplabs/gaufrette && git reset HEAD --hard');
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
     * @Then I should see :fileKey
     * @Then I should see :fileKey in :key
     */
    public function iShouldSeeIn($fileKey, $key = '')
    {
        $outputArray = [];
        $list = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->filesystem->listKeys($key)));
        foreach ($list as $sub) {
            $subArray = $list->getSubIterator();
            if ($subArray === $fileKey) {
                $outputArray[] = iterator_to_array($subArray);
            }
        }

        return count($outputArray) > 0;
    }

    /**
     * @Then I should not see :fileKey
     * @Then I should not see :fileKey in :key
     */
    public function iShouldNotSeeIn($fileKey, $key = '')
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

    /**
     * @Given I use pr :id
     */
    public function iUsePr($id)
    {
        exec(sprintf('cd vendor/knplabs/gaufrette && git stash && git pull origin master && curl -sS https://patch-diff.githubusercontent.com/raw/KnpLabs/Gaufrette/pull/%s.patch | git apply', $id));
        if(!extension_loaded('runkit')){
            throw new \RuntimeException('You should install `runkit` extension to be able to use hot-swap feature');
        }

        runkit_import('vendor/autoload.php', RUNKIT_IMPORT_OVERRIDE);
    }
}

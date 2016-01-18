<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;

class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var \Gaufrette\Filesystem
     */
    private $filesystem;

    /**
     * @var string|null
     */
    private $currentFileContent;

    /**
     * @BeforeSuite
     */
    public function setup()
    {
        DockerFactory::start();
    }

    /**
     * @AfterSuite
     */
    public function teardown()
    {
        DockerFactory::stop();
    }
    /**
     * @BeforeScenario
     * @AfterScenario
     */
    public function cleanup()
    {
        $filesystems = array(AdapterProxyFactory::create('s3'), AdapterProxyFactory::create('local'));
        foreach ($filesystems as $filesystem) {
            foreach ($filesystem->keys() as $file) {
                if ($file === '.gitkeep') {
                    continue;
                }
                if ($filesystem->has($file)) {
                    $filesystem->delete($file);
                }
            }
        }
    }

    /**
     * @Given I use :tech
     */
    public function iUse($tech)
    {
        if ($tech !== 'local') {
            DockerFactory::start($tech);
        }

        $this->filesystem = AdapterProxyFactory::create($tech);
    }

    /**
     * @Given I write :name with content:
     */
    public function fileExistsWithContent($name, PyStringNode $string)
    {
        $this->filesystem->write($name, $string->__toString(), true);
    }

    /**
     * @When I read file :name
     */
    public function iReadFile($name)
    {
        $this->currentFileContent = $this->filesystem->read($name);
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
}

<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Gaufrette\Adapter;

class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
    * @BeforeScenario
    * @AfterScenario
    */
    public function cleanup()
    {
        $adapter = ProxyFactory::create('local');
        foreach ($adapter->keys() as $file) {
            if ($file === '.gitkeep') {
                continue;
            }
            $adapter->delete($file);
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
        $this->adapter = ProxyFactory::create($tech);
    }

    /**
     * @Given file :name exists with content:
     */
    public function fileExistsWithContent($name, PyStringNode $string)
    {
        $this->adapter->write($name, $string->__toString());
    }

    /**
     * @When I read file :name
     */
    public function iReadFile($name)
    {
        $this->currentFile = $this->adapter->read($name);
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $string)
    {
        return strstr($this->currentFile, $string->__toString());
    }
}

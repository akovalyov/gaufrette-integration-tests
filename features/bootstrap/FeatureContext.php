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
     * @var string|null
     */
    private $currentFileContent;
    /**
    * @BeforeScenario
    * @AfterScenario
    */
    public function cleanup()
    {
        $adapters = [AdapterProxyFactory::create('s3'), AdapterProxyFactory::create('local')];
        foreach($adapters as $adapter) {
            foreach ($adapter->keys() as $file) {
                if ($file === '.gitkeep') {
                    continue;
                }
                if($adapter->exists($file)) {
                    $adapter->delete($file);
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
        $this->adapter = AdapterProxyFactory::create($tech);
    }

    /**
     * @Given file :name exists with content:
     */
    public function fileExistsWithContent($name, PyStringNode $string)
    {
        $this->adapter->write($name, $string->__toString(), true);
    }

    /**
     * @When I read file :name
     */
    public function iReadFile($name)
    {
        $this->currentFileContent = $this->adapter->read($name);
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $string)
    {
        if(null === $this->currentFileContent){
            throw new \RuntimeException('No file was read yet');
        }
        return strstr($this->currentFileContent, $string->__toString());
    }
}

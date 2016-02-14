Feature:
  As a Gaufrette user
  I shouldn't have memory leaks when creating a file

  Scenario: I create a file
    Given class "Gaufrette\File" has method "setContent" with arguments "$content, $metadata = array()" and code
    """
      $this->content = $content;
      $this->setMetadata($metadata);

      return $this->size = $this->filesystem->write($this->key, $this->content, true);
    """

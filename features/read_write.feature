Feature:
  As a Gaufrette user
  I should be able to use the read/write methods

  Scenario Outline: Retrieve file contents
    Given I use <tech>
    And I write "test.txt" with content:
      """
      Hello world
      """
    When I read file "test.txt"
    Then I should see:
      """
      Hello world
      """

    Examples:
      | tech |
      | local |
#      | sftp | strange bugs because of libssh2 and opendir. Probably related https://bugs.php.net/bug.php?id=64169
      | s3 |

Feature:
  As a Gaufrette user
  I should be able to use the read method

  Scenario Outline: Retrieve file contents
    Given I use <tech>
    And file "test.txt" exists with content:
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
#      | sftp |
      | s3 |

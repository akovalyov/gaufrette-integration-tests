Feature:
  As a Gaufrette user
  I should be able to use the read/write methods

  Scenario Outline: Read/Write files
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
      | sftp |
      | s3 |


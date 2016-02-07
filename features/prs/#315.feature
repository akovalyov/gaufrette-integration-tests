Feature:
    As a Gaufrette user
    I should be able to rename files on AWS S3

  Scenario: Rename file
    Given I use pr 315
    And I use s3 with adapter s3
    And file "1.txt" exists with content:
    """
      Hello world
    """
    When I rename "1.txt" to "2.txt"
    Then I should see "2.txt"
    Then I should not see "1.txt"

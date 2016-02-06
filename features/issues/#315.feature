Feature:
    As a Gaufrette user
    I should be able to rename files on AWS S3

  Scenario: Rename file
    Given I use s3 with adapter s3
    And file "1.txt" exists
    When I rename "1.txt" to "2.txt"
    Then I should see "2.txt" in keys
    Then I should not see "1.txt" in keys

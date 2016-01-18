Feature: As a Gaufrette user
When I use FTP adapter
I should be able to get list of keys with dirs with baclslash

  Scenario: I get raw listing of files with backslash
    Given I use ftp with adapter ftp
    And file "test" exists
    And file "\test" exists
    When I call method "listDirectory" of current fs adapter
    Then I should see "\test" in keys
    Then I should see "test" in keys

  Scenario: I get raw listing of directories with backslash
    Given I use ftp with adapter ftp
    And dir "test" exists
    And dir "\test" exists
    And dir "\" exists
    And dir "\\" exists
    When I call method "listDirectory" of current fs adapter
    Then I should see "\test" in dirs
    Then I should see "\" in dirs
    Then I should see "\\" in dirs

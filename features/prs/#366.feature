Feature:
  As a Gaufrette user
  I should be able to remove files and dirs in sftp

  Background:
    Given I checkout "yunai39-master"
    And I pull "yunai39/Gaufrette"

  Scenario: Delete file
    Given I use sftp with adapter sftp
    And file "1.txt" exists
    When I rename "1.txt" to "2.txt"
    Then I should see "2.txt" in keys
    Then I should not see "1.txt" in keys

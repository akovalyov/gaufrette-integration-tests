@windows
Feature: As a Gaufrette user
         When I use FTP adapter
         I should be able to use raw listing

    Scenario: I get raw listing
      Given I use ftp with adapter ftp
      And file "1.txt" exists
      And file "2.txt" exists
      And file "3.txt" exists
      And file "4.txt" exists
      And file "5.txt" exists
      Then I should see "1.txt"
      And I should see "2.txt"
      And I should see "3.txt"
      And I should see "4.txt"
      And I should see "5.txt"

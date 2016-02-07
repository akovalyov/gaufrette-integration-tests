Feature:
  As a Gaufrette user
  I should be able to use the main fs methods

  Scenario Outline: Read/Write/Delete files
    Given I use <storage> with adapter <adapter>
      And I write "test.txt" with content:
      """
      Hello world
      """
     When I read file "test.txt"
     Then I should see:
      """
      Hello world
      """
    When I delete file "test.txt"
    Then I should not see "test.txt" in list

    Examples:
      | storage | adapter        |
      | local   | local          |
      | sftp    | sftp_phpseclib |
#      | sftp    | sftp           |
      | s3      | s3             |
      | ftp     | ftp            |
      | gridfs  | gridfs         |

  Scenario Outline: Read/Write/Delete files/folders
    Given I use <storage> with adapter <adapter>
      And I write "folder/test.txt" with content:
      """
      Hello world
      """
     When I read file "folder/test.txt"
     Then I should see:
      """
      Hello world
      """
    When I delete file "folder/test.txt"
    Then I should not see "test.txt" in "folder"

    Examples:
      | storage | adapter        |
      | local   | local          |
      | sftp    | sftp_phpseclib |
#      | sftp    | sftp           |
      | s3      | s3             |
      | ftp     | ftp            |
      | gridfs  | gridfs         |

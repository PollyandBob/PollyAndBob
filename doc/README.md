CODE RULES:
    - Committing comment: "FEN-### Description [NVR]"
    - If commit is not for Code Review (change settings etc) - add NVR
    - Use autocommenting names or comment
    - Do not keep commented code blocks (for future use)
    - PHPDoc
    - Right margin is about 120 characters
    - Use 4 spaces as one tab and it is one depth indent
    - Use @TODO if something is not implemented yet
    - Use this code schema:

class className
{
    public function functionName()
    {
        // FAIL FAST
        if (...) {
            return false;
        }

        if (...) {
            // ...
        } else {
            foreach (...) {
                // ...
            }
        }

        $arrayName = array(
                        'key1'      => $value,
                        'longerkey' => $value2
                    );
        return $arrayName;
    }
}

SYMFONY RULES:
    - Config in yaml format
    - Use annotation in Entities and Templates
    - Discuss adding new external Bundle with the team
    - Please create separate configuration file for each Bundle
    - We do use APC cache
    - We do use query builder


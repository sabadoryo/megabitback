<?php

class User
{

    private $conn;

    private $db_table = "users";

    public $id;
    public $email;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getUsers($attrs)
    {
        $query = $this->generateQuery($attrs);
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getDistinctProviders()
    {
        $query = "SELECT SUBSTRING_INDEX(email,'@',-1) as provider FROM users group by provider";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function createUser()
    {
        $sqlQuery = "INSERT INTO
                        " . $this->db_table . "
                    SET
                        email = :email,
                        created_at = :created_at";

        $stmt = $this->conn->prepare($sqlQuery);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":email", $this->email);
        $date = date('Y-m-d H:i:s');
        $stmt->bindParam(":created_at", $date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function deleteUser()
    {
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    private function generateQuery($attrs)
    {
        $sqlQuery = "SELECT id, email,created_at FROM " . $this->db_table;

        if (isset($attrs['ids'])) {
            $numbers = implode(',', $attrs['ids']);

            $sqlQuery .= " WHERE id IN ($numbers)";

            return $sqlQuery;
        }

        if (isset($attrs['email']) || isset($attrs['ext'])) {
            $sqlQuery .= " WHERE ";
        }

        if (isset($attrs['email']) && isset($attrs['ext'])) {
            $sqlQuery .= "email like '%{$attrs['email']}%'";

            if (isset($attrs['ext'])) {
                $sqlQuery .= " AND email like '%{$attrs['ext']}%'";
            }

            return $sqlQuery;
        }

        if (isset($attrs['email'])) {
            $sqlQuery .= " email like '%{$attrs['email']}%'";
        }

        if (isset($attrs['ext'])) {
            $sqlQuery .= " email like '%{$attrs['ext']}%'";
        }

        if (isset($attrs['sortDir']) && isset($attrs['sortBy'])) {
            $sqlQuery .= " ORDER BY {$attrs['sortBy']} {$attrs['sortDir']}";
        }

        $offset = ($attrs['page'] - 1) * $attrs['perPage'];
        $sqlQuery .= " LIMIT {$attrs['perPage']} OFFSET $offset";
        return $sqlQuery;
    }

    public function getTotalEmailsNum()
    {
        $query = "SELECT count(*) FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
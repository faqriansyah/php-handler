<?php

class CRUDHandler {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        $dsn = "mysql:host=$host;dbname=$dbname";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function create($data) {
        $sql = "INSERT INTO table_name (column1, column2, column3) VALUES (:column1, :column2, :column3)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'column1' => $data['column1'],
            'column2' => $data['column2'],
            'column3' => $data['column3'],
        ]);
        return $this->pdo->lastInsertId();
    }

    public function read($id) {
        $sql = "SELECT * FROM table_name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $sql = "UPDATE table_name SET column1 = :column1, column2 = :column2, column3 = :column3 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'column1' => $data['column1'],
            'column2' => $data['column2'],
            'column3' => $data['column3'],
        ]);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $sql = "DELETE FROM table_name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
?>

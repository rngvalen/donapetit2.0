<?php

class Database
{
    private string $host = 'localhost';
    private string $db_name = 'donappetit';
    private string $username = 'root';
    private string $password = '';
    private ?\PDO $conn = null;

    public function getConnection(): \PDO
    {
        if ($this->conn instanceof \PDO) {
            return $this->conn;
        }

        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $this->host,
                $this->db_name
            );

            $this->conn = new \PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $exception) {
            throw new \RuntimeException(
                'Error de conexión a la base de datos: ' . $exception->getMessage(),
                (int)$exception->getCode(),
                $exception
            );
        }

        return $this->conn;
    }
}

return (new Database())->getConnection();
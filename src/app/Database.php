<?php
declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;
use PDOException;

class Database
{
    /**
     * @var PDO
     */
    private PDO $connection;

    public function __construct(string $dsn, string $username = '', string $password = '')
    {
        try {
            $this->connection = new PDO($dsn, $username, $password);
        } catch (PDOException $exeption) {
            throw new InvalidArgumentException('Database error:' . $exeption->getMessage());
        }

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }


}


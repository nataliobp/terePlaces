<?php

namespace TerePlaces\Infrastructure\CountService;

use TerePlaces\Domain\RequestCountService;

class PdoCountService implements RequestCountService
{
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getCountFromToday(): int
    {
        $sql = <<<'SQL'
            SELECT num_searches FROM searches_count WHERE day = :day
SQL;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['day' => (new \DateTime())->format('Y-m-d')]);

        return (int) $stmt->fetchColumn();
    }

    public function incrementCountFromToday()
    {
        $currentCount = $this->getCountFromToday();

        $sql = empty($currentCount)
            ? 'INSERT INTO searches_count(num_searches, day) VALUES (:num_searches, :day)'
            : 'UPDATE searches_count SET num_searches = :num_searches WHERE day = :day';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['num_searches' => $currentCount + 1, 'day' => (new \DateTime())->format('Y-m-d')]);
    }
}

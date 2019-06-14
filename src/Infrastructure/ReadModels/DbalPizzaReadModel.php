<?php

declare(strict_types=1);

namespace Infrastructure\ReadModels;

use Application\ReadModels\Pizza;
use Application\ReadModels\PizzaNotFound;
use Application\ReadModels\PizzaReadModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Domain\DTO\PizzaName;
use Domain\DTO\Price;
use Domain\DTO\Topping;
use Domain\DTO\Toppings;
use Domain\PizzaId;
use Generator;

final class DbalPizzaReadModel implements PizzaReadModel
{
    public const TABLE = 'pizzas';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * @param PizzaId $pizzaId
     *
     * @return Pizza
     */
    public function find(PizzaId $pizzaId): Pizza
    {
        $stmt = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('pizza_id = :pizza_id')
            ->setParameters([
                ':pizza_id' => (string) $pizzaId,
            ])
            ->execute()
        ;

        if (!$row = $stmt->fetch()) {
            throw PizzaNotFound::with($pizzaId);
        }

        return $this->toPizzaProjection($row);
    }

    /**
     * @return Pizza[]
     */
    public function all(): iterable
    {
        $stmt = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute()
        ;

        while (!$row = $stmt->fetch()) {
            yield $this->toPizzaProjection($row);
        }
    }

    /**
     * @param PizzaId   $pizzaId
     * @param PizzaName $name
     *
     * @throws DBALException
     */
    public function createPizza(PizzaId $pizzaId, PizzaName $name): void
    {
        $this->connection->insert(
            self::TABLE,
            [
                'pizza_id' => $pizzaId->toString(),
                'name' => $name->getName(),
            ],
            $this->getTypes()
        );
    }

    /**
     * @param PizzaId $pizzaId
     * @param Price   $price
     *
     * @throws DBALException
     */
    public function increasePrice(PizzaId $pizzaId, Price $price): void
    {
        $this->connection->executeUpdate(
            sprintf('UPDATE %s SET price = COALESCE(price, 0) + CAST(:price as integer) WHERE pizza_id = :pizza_id', self::TABLE),
            [
                'price' => $price->getCents(),
                'pizza_id' => $pizzaId->toString(),
            ],
            $this->getTypes()
        );
    }

    /**
     * @param PizzaId $pizzaId
     * @param Topping $topping
     *
     * @throws DBALException
     */
    public function addTopping(PizzaId $pizzaId, Topping $topping): void
    {
        $this->connection->executeUpdate(
            sprintf("UPDATE %s SET toppings = trim(',' from concat(toppings, ',', CAST(:topping as varchar))) WHERE pizza_id = :pizza_id", self::TABLE),
            [
                'topping' => $topping->getName(),
                'pizza_id' => $pizzaId->toString(),
            ],
            $this->getTypes()
        );
    }

    /**
     * @param $row
     *
     * @return Pizza
     */
    private function toPizzaProjection(array $row): Pizza
    {
        foreach ($this->getTypes() as $key => $type) {
            $row[$key] = $this->connection->convertToPHPValue($row[$key], $type);
        }

        return new Pizza(
            PizzaId::fromString($row['pizza_id']),
            new PizzaName($row['name']),
            new Price($row['price']),
            new Toppings(...$this->toToppingsProjection($row))
        );
    }

    private function toToppingsProjection(array $row): Generator
    {
        foreach ($row['toppings'] as $topping) {
            yield new Topping($topping);
        }
    }

    /**
     * @return array
     */
    private function getTypes(): array
    {
        return [
            'pizza_id' => Type::GUID,
            'name' => Type::STRING,
            'price' => Type::SMALLINT,
            'toppings' => Type::SIMPLE_ARRAY,
        ];
    }
}

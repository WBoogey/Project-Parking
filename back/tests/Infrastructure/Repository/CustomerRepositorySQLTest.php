<?php

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repository\CustomerRepositorySQL;
use App\Domain\Customer\Customer;

class CustomerRepositorySQLTest extends TestCase
{
    private \PDO $pdo;
    private CustomerRepositorySQL $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=parking_db', 'root', '');
        $this->repo = new CustomerRepositorySQL($this->pdo);

        $this->pdo->exec("DELETE FROM customers");
        $this->pdo->exec("DELETE FROM users");
    }

    public function testSaveAndFindCustomer()
    {
        // Arrange
        $customer = new Customer(1, 'foo@test.com', 'mysecret', 'Jean', 'Dupont');
        $this->repo->save($customer);

        // Act
        $found = $this->repo->findById(1);

        // Assert
        $this->assertNotNull($found);
        $this->assertEquals('foo@test.com', $found->getEmail());
        $this->assertEquals('Jean', $found->getFirstName());
        $this->assertEquals('Dupont', $found->getLastName());
        $this->assertEquals('mysecret', $found->getPassword());
    }

    public function testFindByEmail()
    {
        $this->repo->save(new Customer(2, 'bar@example.com', 'pass', 'Marie', 'Durand'));
        $found = $this->repo->findByEmail('bar@example.com');
        $this->assertNotNull($found);
        $this->assertEquals('Marie', $found->getFirstName());
    }

    public function testDelete()
    {
        $customer = new Customer(3, 'del@ex.fr', 'xxx', 'Del', 'Test');
        $this->repo->save($customer);

        $this->repo->delete($customer);
        $this->assertNull($this->repo->findById(3));
    }
    
}

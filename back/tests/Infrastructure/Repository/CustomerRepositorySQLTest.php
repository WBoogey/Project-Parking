<?php

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repository\CustomerRepositorySQL;
use App\Domain\Customer\Customer;
// … autres use comme Stationing, Reservation, etc.

class CustomerRepositorySQLTest extends TestCase
{
    private \PDO $pdo;
    private CustomerRepositorySQL $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=parking_db', 'root', '');
        $this->repo = new CustomerRepositorySQL($this->pdo);

        // Optionnel : purger la table customers, ou travailler en transaction rollbackable
    }

    public function testSaveAndFindCustomer()
    {
        // Arrange
        $customer = new Customer(1, 'foo@test.com', 'password', 'Jean', 'Dupont');
        $this->repo->save($customer);

        // Act
        $found = $this->repo->findById(1);

        // Assert
        $this->assertNotNull($found);
        $this->assertEquals('Jean', $found->getFirstName());
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
    
    // À faire : tests pour getReservations / getSubscriptions / getStationings
    // Ces méthodes supposent que les tables reservations, subscriptions, stationings ont des données liées au customer testé.
    // On peut ajouter les lignes correspondantes directement dans la base de test ou dans le test setUp
}

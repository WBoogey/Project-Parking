<?php

declare(strict_types=1);

use App\Domain\Parking\Application\GetAllParkingsWithRates;
use App\Domain\Parking\Application\GetParkingById;
use App\Infrastructure\HTTP\ParkingController;

// Initialize use-cases
$getAllParkingsWithRatesUseCase = new GetAllParkingsWithRates($parkingRepository, $rateRepository);
$getParkingByIdUseCase = new GetParkingById($parkingRepository, $rateRepository);

// Initialize controller
$parkingController = new ParkingController(
    $getAllParkingsWithRatesUseCase,
    $getParkingByIdUseCase
);

// Public route for listing all parkings with their rates
$router->get(
    '/api/parkings',
    [$parkingController, 'index'],
    'api.parkings.index'
);

// Public route for getting a single parking by ID
$router->get(
    '/api/parkings/:id',
    [$parkingController, 'show'],
    'api.parkings.show'
);

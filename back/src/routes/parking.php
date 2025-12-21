<?php

declare(strict_types=1);

use App\Domain\Parking\Application\GetAllParkingsWithRates;
use App\HTTP\ParkingController;

// Initialize use-case
$getAllParkingsWithRatesUseCase = new GetAllParkingsWithRates($parkingRepository, $rateRepository);

// Initialize controller
$parkingController = new ParkingController($getAllParkingsWithRatesUseCase);

// Public route for listing all parkings with their rates
$router->get(
    '/api/parkings',
    [$parkingController, 'index'],
    'api.parkings.index'
);

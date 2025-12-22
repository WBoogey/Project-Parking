<?php

declare(strict_types=1);

use App\Domain\Rate\Application\CreateRate;
use App\Domain\Rate\Application\DeleteRate;
use App\Domain\Rate\Application\GetParkingRates;
use App\Domain\Rate\Application\UpdateRate;
use App\Infrastructure\HTTP\RateController;
use App\Services\RateService;

// Initialize use-cases
$createRateUseCase = new CreateRate($rateRepository, $parkingRepository);
$updateRateUseCase = new UpdateRate($rateRepository, $parkingRepository);
$deleteRateUseCase = new DeleteRate($rateRepository, $parkingRepository);
$getParkingRatesUseCase = new GetParkingRates($rateRepository, $parkingRepository);

// Initialize service and controller
$rateService = new RateService(
    $createRateUseCase,
    $updateRateUseCase,
    $deleteRateUseCase,
    $getParkingRatesUseCase
);
$rateController = new RateController($rateService);

// Owner routes for managing rates
$router->get(
    '/api/owner/parkings/:parkingId/rates',
    [$rateController, 'index'],
    'api.owner.parkings.rates.index'
);

$router->post(
    '/api/owner/parkings/:parkingId/rates',
    [$rateController, 'store'],
    'api.owner.parkings.rates.store'
);

$router->put(
    '/api/owner/parkings/:parkingId/rates/:rateId',
    [$rateController, 'update'],
    'api.owner.parkings.rates.update'
);

$router->delete(
    '/api/owner/parkings/:parkingId/rates/:rateId',
    [$rateController, 'destroy'],
    'api.owner.parkings.rates.destroy'
);

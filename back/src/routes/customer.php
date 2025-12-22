<?php

use App\Infrastructure\HTTP\CustomerController;
use App\Services\CustomerService;
use App\Domain\Customer\Application\GetCustomerReservations;
use App\Domain\Customer\Application\GetCustomerSubscriptions;
use App\Domain\Customer\Application\GetCustomerStationings;

$getReservationsUseCase = new GetCustomerReservations($customerRepository);
$getSubscriptionsUseCase = new GetCustomerSubscriptions($customerRepository);
$getStationingsUseCase = new GetCustomerStationings($customerRepository);

$customerService = new CustomerService(
    $getReservationsUseCase,
    $getSubscriptionsUseCase,
    $getStationingsUseCase,
);

$customerController = new CustomerController($customerService);

$router->get(
    "/api/customer/reservations",
    [$customerController, "reservations"],
    "api.customer.reservations",
);

$router->get(
    "/api/customer/subscriptions",
    [$customerController, "subscriptions"],
    "api.customer.subscriptions",
);

$router->get(
    "/api/customer/stationings",
    [$customerController, "stationings"],
    "api.customer.stationings",
);

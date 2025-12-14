<?php

use App\HTTP\OwnerController;
use App\Services\OwnerService;
use App\Domain\Owner\Application\GetOwnerParkings;
use App\Domain\Owner\Application\AddParkingToOwner;
use App\Domain\Owner\Application\RemoveParkingFromOwner;

$getOwnerParkingsUseCase = new GetOwnerParkings($ownerRepository);
$addParkingToOwnerUseCase = new AddParkingToOwner($ownerRepository);
$removeParkingFromOwnerUseCase = new RemoveParkingFromOwner($ownerRepository);

$ownerService = new OwnerService(
    $getOwnerParkingsUseCase,
    $addParkingToOwnerUseCase,
    $removeParkingFromOwnerUseCase
);
$ownerController = new OwnerController($ownerService);

$router->get(
    "/api/owner/parkings",
    [$ownerController, "getParkings"],
    "api.owner.parkings.list"
);

$router->post(
    "/api/owner/parkings",
    [$ownerController, "addParking"],
    "api.owner.parkings.add"
);

$router->delete(
    "/api/owner/parkings",
    [$ownerController, "removeParking"],
    "api.owner.parkings.remove"
);

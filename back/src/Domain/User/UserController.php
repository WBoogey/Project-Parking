<?php

namespace App\Domain\User;

use Core\Controllers;

class UserController extends Controllers{

  public function user(){
    echo $this->json(response_code: 200,data: [
      'id' => '1',
      'fistname' => 'Christ-Yvann',
      'lastname' => 'Ehoura',
      'roles' => 'owner',
      'Parking_Id' => '2'
    ]);
  }
}
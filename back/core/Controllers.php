<?php

namespace Core;

abstract class Controllers{
  
  // methode json pour retourner les data en json et une réponse http claire
  public function json(int $response_code, array $data){
    http_response_code($response_code);
    json_encode($data);
  }

  public function success($data = null, $message = 'Success') {
      return $this->json(200, [
          'status' => 'success',
          'message' => $message,
          'data' => $data
      ]);
  }


  // erreur 404
  public function notFound($message = 'Resource not found') {
      return $this->json(404, [
          'status' => 'error',
          'message' => $message
      ]);
  }

  // nettoyer les données
  public function sanitize($data) {
      if (is_array($data)) {
          return array_map([$this, 'sanitize'], $data);
      }
      return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
  }
}
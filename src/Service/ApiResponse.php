<?php

namespace App\Service;

class ApiResponse
{
  private array $params;

  private $paramsRequest = [];

  public bool $hasError = false;

  private array $response = [];

  public function setParams(array $params)
  {
    $this->params = $params;
  }

  public function isParamsExistAndCorrectType($request)
  {
    foreach ($this->params as $key => $options) {
      if (!isset($options["required"])) {
        $options["required"] = true;
      }

      if ($options["required"] !== false || !is_null($request->query->get($key))) {
        $this->paramsRequest[$key] = $request->query->get($key);
        switch ($options['type']) {
          case "integer":
            $goodType = is_numeric($this->paramsRequest[$key]);
            $differentAfterConversion = intval($this->paramsRequest[$key]) != $this->paramsRequest[$key];
            break;
  
          case "string":
            $goodType = is_string($this->paramsRequest[$key]);
            $differentAfterConversion = false;
            break;
  
          case "bool":
            $goodType = ($this->paramsRequest[$key] === "true" || $this->paramsRequest[$key] === "false");
            $differentAfterConversion = false;
            break;
  
          default:
            $goodType = true;
            $differentAfterConversion = false;
            break;
        }

        if (!$this->paramsRequest[$key] || !$goodType || $differentAfterConversion) {
          $this->response["errors"]["code"] = 400;
          $this->response["errors"]["type"] = "params_invalid";
          $this->response["errors"]["message"][] = $options["required"] ? "${key} is required and must be type of " . $options['type'] : "${key} must be type of " . $options['type'];
          $this->hasError = true;
        }
      }
    }

    if ($this->hasError) {
      return $this->response;
    }

    return $this->paramsRequest;
  }

  public function isBodyExistAndCorrectType($request)
  {
    foreach ($this->params as $key => $type) {
      $this->paramsRequest[$key] = $request->request->get($key);
      switch ($type) {
        case "integer":
          $goodType = is_numeric($this->paramsRequest[$key]);
          $differentAfterConversion = intval($this->paramsRequest[$key]) != $this->paramsRequest[$key];
          break;

        case "string":
          $goodType = is_string($this->paramsRequest[$key]);
          $differentAfterConversion = false;
          break;

        case "bool":
          $goodType = is_bool($this->paramsRequest[$key]);
          $differentAfterConversion = false;
          break;

        default:
          $goodType = true;
          $differentAfterConversion = false;
          break;
      }
      if (!$this->paramsRequest[$key] || !$goodType || $differentAfterConversion) {
        $this->response["errors"]["code"] = 400;
        $this->response["errors"]["type"] = "params_invalid";
        $this->response["errors"]["message"][] = "${key} is required and must be type of ${type}";
        $this->hasError = true;
      }
    }

    if ($this->hasError) {
      return $this->response;
    }

    return $this->paramsRequest;
  }
}

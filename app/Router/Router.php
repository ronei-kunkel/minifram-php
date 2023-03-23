<?php

namespace Minifram\Router;

use Minifram\Http\Request;
use Minifram\Http\Response;

class Router {

  private static $allowedMethods = [];

  private static $allowedResources = [];

  private static $allowedResourcesActions = [];

  private static $allowedIdsInResources = [];

  private static $methods = [];

  private static $routes = [];

  private static $callbacks = [];

  private static $authRoutes = [];

  private static $currentCallback = null;

  private static $currentRouteIndex = null;

  // TODO: implement property called baseRoute 

  // TODO: implement method called setBaseRoute to verify if the request coming from api or web


  public static function get($route, $callback, $authRoute = false) {
    self::create(strtoupper(__FUNCTION__), $route, $callback, $authRoute);
  }

  public static function post($route, $callback, $authRoute = false) {
    self::create(strtoupper(__FUNCTION__), $route, $callback, $authRoute);
  }

  public static function put($route, $callback, $authRoute = false) {
    self::create(strtoupper(__FUNCTION__), $route, $callback, $authRoute);
  }

  public static function patch($route, $callback, $authRoute = false) {
    self::create(strtoupper(__FUNCTION__), $route, $callback, $authRoute);
  }

  public static function delete($route, $callback, $authRoute = false) {
    self::create(strtoupper(__FUNCTION__), $route, $callback, $authRoute);
  }

  private static function create($method, $route, $callback, $authRoute) {
    // TODO: implement validation to create only route and method not exists and if exists throw error

    self::setAllowedMethod($method);
    self::setAllowedResource($route);
    self::setAllowedResourceAction($route);
    self::setAllowedIdInResource($route);

    self::$methods[]    = $method;
    self::$routes[]     = $route;
    self::$callbacks[]  = $callback;
    self::$authRoutes[] = $authRoute;
  }

  public static function run(Request $request) {
    try {

      self::setCurrentCallback($request);

      self::validateAuthRequired($request);
      self::validateMethod($request);
      self::validateResource($request);
      self::validateResourceId($request);
      self::validateResourceAction($request);
      self::validateCallback($request);
      self::validateHeaders($request);

      call_user_func(self::$currentCallback, $request);

    } catch (\Exception $e) {
      (new Response($request))->return(['error' => $e->getMessage()], $e->getCode());
    }
  }

  /**
   * validation methods
   */

  private static function validateMethod($request) {
    if(!in_array($request->getMethod(), self::$allowedMethods)) 
      throw new \Exception("Method '".$request->getMethod()."' not allowed!", 405);
  }

  private static function validateResource($request) {
    if(!in_array($request->getResource(), self::$allowedResources)) 
      throw new \Exception("'".$request->getResource()."' isn't a valid resource!", 404);
  }

  private static function validateResourceId($request) {
    if ($request->getId() === '0')
    throw new \Exception("'/".$request->getResource()."' need id greater than 0!", 400);
    
    if(!in_array($request->getResource(), self::$allowedIdsInResources) and !is_null($request->getId()))
    throw new \Exception("'".$request->getResource()."' have no support for id!", 400);
  }

  private static function validateResourceAction($request) {
    if(is_null($request->getAction())) return;

    if(!is_null($request->getAction()) and !in_array($request->getAction(), self::$allowedResourcesActions[$request->getResource()]) and !is_null($request->getId())) 
      throw new \Exception("'".$request->getResource()."' have no support for action '".$request->getAction()."'!", 501);
  }

  private static function validateCallback($request) {
    $requestFromApi = $request->getFrom() === 'api';

    $baseRoute = ($requestFromApi) ? '/api' : '';

    if (empty(self::$currentCallback))
      throw new \Exception("Have no callback for route '". $baseRoute .self::$routes[self::$currentRouteIndex]."' with '".self::$methods[self::$currentRouteIndex]."' defined in '" . $request->getFrom() . "' routes file!", 500);

    $callback = explode('::', self::$currentCallback);

    if (!class_exists($callback[0]))
      throw new \Exception("Controller of callback for route '". $baseRoute .self::$routes[self::$currentRouteIndex]."' with '".self::$methods[self::$currentRouteIndex]."' defined in '" . $request->getFrom() . "' routes file doesn't exists!", 500);

    if (!method_exists($callback[0], $callback[1]))
      throw new \Exception("Method of callback for route '". $baseRoute .self::$routes[self::$currentRouteIndex]."' with '".self::$methods[self::$currentRouteIndex]."' defined in '" . $request->getFrom() . "' routes file doesn't exists!", 500);
  }

  private static function validateHeaders($request) {
    $requestFromApi = $request->getFrom() === 'api';
    if ($requestFromApi and !isset($request->getHeaders()['Content-Type']))
      throw new \Exception("Content-Type header are missing", 403);

    if ($requestFromApi and $request->getHeaders()['Content-Type'] !== 'application/json')
      throw new \Exception("Content-Type it must be application/json!", 403);

    if ($requestFromApi and in_array($request->getMethod(), ['PUT', 'POST']) and !isset($request->getHeaders()['Content-Length']))
      throw new \Exception("Content-Length header are missing", 411);
  }

  private static function validateAuthRequired($request) {
    if (self::$authRoutes[self::$currentRouteIndex] and !isset($request->getHeaders()['token']))

      throw new \Exception('token header are missing', 401);
  }

  /**
   * current values
   */

  private static function setCurrentCallback($request) {

    // TODO: extract to new method called getIndexesMatchingMethods
    $indexesMatchingMethods = [];
    foreach (self::$methods as $i => $method) {
      if ($method !== $request->getMethod()) continue;

      $indexesMatchingMethods[] = $i;
    }
    // until here

    // TODO: extract to new method called getMatchedIndex
    $matchedIndex = null;
    foreach (self::$routes as $i => $route) {
      if (!in_array($i, $indexesMatchingMethods)) continue;

      if (!empty($request->getId())) $route = str_replace(':id', $request->getId(), $route);

      if ($route != $request->getRoute()) continue;

      $matchedIndex = $i;
    }

      // TODO: extract to new method called validateMethodCalledForRoute inside of getMatchedIndex
    $requestFromApi = $request->getFrom() === 'api';
    $baseRoute = ($requestFromApi) ? '/api' : '';

    if(!is_numeric($matchedIndex) or $matchedIndex < 0) 
      throw new \Exception("Method '".$request->getMethod()."' not allowed for route '" . $baseRoute . $request->getRoute() . "'!", 405);
      // until here
    // untill here
    
    self::$currentCallback = self::$callbacks[$matchedIndex];
    self::setCurrentRouteIndex($matchedIndex);
  }

  private static function setCurrentRouteIndex($index) {
    self::$currentRouteIndex = $index;
  }


  /**
   * routes configs
   */

  private static function setAllowedMethod(string $method) {
    if (!in_array($method, self::$allowedMethods)) self::$allowedMethods[] = $method;
  }

  private static function setAllowedResource(string $route) {
    $route = array_values(array_filter(explode('/', $route)));

    if (!isset($route[0])) $route[0] = '/';

    if (!in_array($route[0], self::$allowedResources)) self::$allowedResources[] = $route[0];
  }

  private static function setAllowedResourceAction(string $route) {
    $route = array_values(array_filter(explode('/', $route)));

    if (!isset($route[2])) return;

    if (!isset(self::$allowedResourcesActions[$route[0]])) self::$allowedResourcesActions[$route[0]] = [];

    if (!in_array($route[2], self::$allowedResourcesActions[$route[0]])) self::$allowedResourcesActions[$route[0]][] = $route[2];
  }

  private static function setAllowedIdInResource(string $route) {
    $route = array_values(array_filter(explode('/', $route)));

    if (!isset($route[1])) return;

    if (!in_array($route[0], self::$allowedIdsInResources)) self::$allowedIdsInResources[] = $route[0];
  }
}

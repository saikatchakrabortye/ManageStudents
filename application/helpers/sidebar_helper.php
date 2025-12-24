<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('is_active_page')) {
    /**
     * Check if current page matches sidebar menu item
     * 
     * @param string $routeKey The route_key from sidebar database
     * @return bool True if current page matches the route_key
     */
    function is_active_page($routeKey)
    {
        $CI =& get_instance();
        
        // Get current controller and method
        $currentController = $CI->router->fetch_class();
        $currentMethod = $CI->router->fetch_method();
        
        // Build current route pattern
        $currentRoute = $currentController . '/' . $currentMethod;
        
        // METHOD 1: Direct controller match (simplest)
        // If routeKey matches current controller name
        if (strtolower($routeKey) == strtolower($currentController)) {
            return true;
        }
        
        // METHOD 2: Check against routes config
        // Load routes if not already loaded
        $routes = $CI->config->item('routes');
        if (!empty($routes) && isset($routes[$routeKey])) {
            $mappedRoute = $routes[$routeKey];
            
            // Check if mapped route matches current route
            if ($mappedRoute == $currentRoute) {
                return true;
            }
            
            // Also check just controller match for routes like 'dashboard' => 'home/index'
            $mappedParts = explode('/', $mappedRoute);
            if (!empty($mappedParts[0]) && strtolower($mappedParts[0]) == strtolower($currentController)) {
                return true;
            }
        }
        
        // METHOD 3: Check URI segments for additional matching
        $uriString = $CI->uri->uri_string();
        
        // Remove trailing slash if exists
        $uriString = trim($uriString, '/');
        
        // Convert routeKey to lowercase for case-insensitive comparison
        $routeKeyLower = strtolower($routeKey);
        
        // Check if routeKey appears in the URI path
        if ($routeKeyLower == $uriString) {
            return true;
        }
        
        // Check if routeKey matches first segment of URI
        $uriSegments = explode('/', $uriString);
        if (!empty($uriSegments[0]) && strtolower($uriSegments[0]) == $routeKeyLower) {
            return true;
        }
        
        // Special case for dashboard if it's mapped to home controller
        if ($routeKeyLower == 'dashboard' && $currentController == 'home') {
            return true;
        }
        
        return false;
    }
}

if (!function_exists('get_current_route')) {
    /**
     * Get current route for debugging
     */
    function get_current_route()
    {
        $CI =& get_instance();
        return $CI->router->fetch_class() . '/' . $CI->router->fetch_method();
    }
}
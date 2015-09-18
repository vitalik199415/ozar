<?php
/**
 * @copyright Артюх Антон 2009
 * @site http://tovit.livejournal.com 
 */
/**
 * Build url
 *
 * @param string Name of rule or url-string
 * @param array Array of params
 * @return string URL
 **/
function site_url($urlname, $params = NULL)
{
    $CI = & get_instance();
    if($params !== NULL && method_exists($CI->router, 'buildUrl'))
    {
        return $CI->router->buildUrl($urlname, $params);
    } else
    {
        return $CI->config->site_url($urlname);
    }
}
?>
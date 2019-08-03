<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_ktbtracker
 *
 * @copyright   Copyright (C) 2012-${COPYR_YEAR} David Uczen Photography, Inc. All rights reserved.
 * @license     GNU General Public License (GPL) version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Cache\Exception\CacheExceptionInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;


/**
 * 
 */
class KTBTrackerController extends BaseController
{
    /**
     * Typical view method for MVC based architecture
     *
     * This function is provide as a default implementation, in most cases
     * you will need to override it in your own controllers.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  BaseController  A \JControllerLegacy object to support chaining.
     *
     * @since   3.0
     */
    public function display($cachable = false, $urlparams = array())
    {
        $document = Factory::getDocument();
        $viewType = $document->getType();
        $viewName = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');
        
        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
        
        // Get/Create the model
        if ($model = $this->getModel($viewName))
        {
            // Push the model into the view (as default)
            $view->setModel($model, true);
            
            if ($cycleModel = $this->getModel('Cycle'))
            {
                $view->setModel($cycleModel);
            }
            
            if ($reqmntModel = $this->getModel('Requirement'))
            {
                $view->setModel($reqmntModel);
            }
            
        }
        
        $view->document = $document;
        
        // Display the view
        if ($cachable && $viewType !== 'feed' && Factory::getConfig()->get('caching') >= 1)
        {
            $option = $this->input->get('option');
            
            if (is_array($urlparams))
            {
                $app = Factory::getApplication();
                
                if (!empty($app->registeredurlparams))
                {
                    $registeredurlparams = $app->registeredurlparams;
                }
                else
                {
                    $registeredurlparams = new \stdClass;
                }
                
                foreach ($urlparams as $key => $value)
                {
                    // Add your safe URL parameters with variable type as value {@see \JFilterInput::clean()}.
                    $registeredurlparams->$key = $value;
                }
                
                $app->registeredurlparams = $registeredurlparams;
            }
            
            try
            {
                /** @var \JCacheControllerView $cache */
                $cache = Factory::getCache($option, 'view');
                $cache->get($view, 'display');
            }
            catch (CacheExceptionInterface $exception)
            {
                $view->display();
            }
        }
        else
        {
            $view->display();
        }
        
        return $this;
    }
}
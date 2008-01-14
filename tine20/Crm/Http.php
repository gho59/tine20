<?php
/**
 * backend class for Egwbase_Http_Server
 *
 * This class handles all Http requests for the Crm application
 *
 * @package     Crm
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @author      Thomas Wadewitz <t.wadewitz@metaways.de>
 * @copyright   Copyright (c) 2007-2007 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id: Http.php 135 2007-09-26 13:37:11Z twadewitz $
 *
 */
class Crm_Http extends Egwbase_Application_Http_Abstract
{
    protected $_appname = 'Crm';
      
    
	public function editProject($_projectId)
	{
         if(empty($_projectId)) {
            $_projectId = NULL;
        }
	    
	    $locale = Zend_Registry::get('locale');
		$currentAccount = Zend_Registry::get('currentAccount');
	    
		$view = new Zend_View();
		 
		$view->setScriptPath('Egwbase/views');
		$view->formData = array();
        
		$list = $locale->getTranslationList('Dateformat');
		$view->formData['config']['dateFormat'] = str_replace(array('dd', 'MMMM', 'MMM','MM','yyyy','yy'), array('d','F','M','m','Y','y'), $list['long']);

		$crmJson = new Crm_Json;		
//		$view->formData['config']['initialTree'] = $eventschedulerJson->getInitialTree('mainTree');

		$view->jsIncludeFiles = array('extjs/build/locale/ext-lang-'.$locale->getLanguage().'.js');
		$view->cssIncludeFiles = array();
		
		$controller = Crm_Controller::getInstance();
		$projects = Crm_Backend_Factory::factory(Crm_Backend_Factory::SQL);
		if($_projectId !== NULL && $project = $projects->getProjectById($_projectId)) {
			$view->formData['values'] = $project->toArray();
			$folder = Egwbase_Container::getInstance()->getContainerById($project->pj_owner);
			
            $_products = $projects->getProductsById($_projectId);
            $view->formData['values']['products'] = $_products->toArray();
           
			$view->formData['config']['folderName']   = $folder->container_name;
			$view->formData['config']['folderRights'] = $folder->account_grants;
		    
		} else {
            $view->formData['values'] = $controller->getEmptyLead()->toArray();
            $view->formData['values']['products'] = array();                
            
            $personalFolders = $projects->getFoldersByOwner($currentAccount->account_id);
		    foreach($personalFolders as $folder) {
		        $view->formData['values']['pj_owner']     = $folder->container_id;
    		    $view->formData['config']['folderName']   = $folder->container_name;
    		    $view->formData['config']['folderRights'] = 31;
                break;
		    }
		    
		}

		$_leadTypes = $projects->getLeadtypes('pj_leadtype','ASC');
		$view->formData['comboData']['leadtypes'] = $_leadTypes->toArray();
		
		$_leadStates =  $projects->getLeadStates('pj_leadstate','ASC');
		$view->formData['comboData']['leadstates'] = $_leadStates->toArray();
		
		$_leadSources =  $projects->getLeadSources('pj_leadsource','ASC');
		$view->formData['comboData']['leadsources'] = $_leadSources->toArray();

		$_productSource =  $projects->getProductsAvailable('pj_productsource','ASC');
		$view->formData['comboData']['productsource'] = $_productSource->toArray();


		$view->jsIncludeFiles[] = 'Crm/js/Crm.js';
		$view->cssIncludeFiles[] = 'Crm/css/Crm.css';
		$view->jsExecute = 'Egw.Crm.ProjectEditDialog.display();';

		$view->configData = array(
            'timeZone' => Zend_Registry::get('userTimeZone'),
            'currentAccount' => Zend_Registry::get('currentAccount')->toArray()
        );
        
		$view->title="edit project";

		header('Content-Type: text/html; charset=utf-8');
		echo $view->render('popup.php');
	}

}
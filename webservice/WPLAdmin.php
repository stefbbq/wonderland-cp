<?php
require_once 'autoload.inc.php';

use wpl\wplPortal\AdminManager;
use sdg\data\Result;

$manager = new AdminManager();

$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : false;

$result = new Result();


switch ($action) {
    case 'addClient':
        $result = $manager->saveClient(
                getValue('name'), 
                getValue('address'), 
                getValue('city'), 
                getValue('province'), 
                getValue('postal_code'), 
                getValue('email'), 
                getValue('phone'), 
                getValue('phone2'), 
                getValue('wplEmail')
                );
        break;
    case 'updateClient':
        $result = $manager->saveClient(
                getValue('name'), 
                getValue('address'), 
                getValue('city'), 
                getValue('province'), 
                getValue('postal_code'), 
                getValue('email'), 
                getValue('phone'), 
                getValue('phone2'), 
                getValue('wplEmail'),
                getValue('guid')
                );
        break;
    case 'deactivateClient':
      $result = $manager->deactivateClient(getValue('guid'));
      break;
    case 'reactivateClient':
      $result = $manager->reactivateClient(getValue('guid'));
      break;
    case 'clientList':
        $result = $manager->getClientList(getValue('s'), getValue('c'));
        break;
    case 'clientSearch':
        $result = $manager->searchClients(getValue('q'), getValue('s'), getValue('c'));
        break;
    case 'clientDetail':
        $result = $manager->getClientDetail(getValue('q'));
        break;
     case 'addClientUser':
        $result = $manager->saveClient(
                getValue('name'), 
                getValue('address'), 
                getValue('city'), 
                getValue('province'), 
                getValue('postal_code'), 
                getValue('email'), 
                getValue('phone'), 
                getValue('phone2'), 
                getValue('wplEmail')
                );
        break;
    case 'updateClientUser':
        $result = $manager->saveClient(
                getValue('name'), 
                getValue('address'), 
                getValue('city'), 
                getValue('province'), 
                getValue('postal_code'), 
                getValue('email'), 
                getValue('phone'), 
                getValue('phone2'), 
                getValue('wplEmail'),
                getValue('guid')
                );
        break;
     
    default : 
        $result->success = false;
        $result->message = 'no action';
        break;
}

function getValue($name) {
  if (isset($_GET[$name])) {
    return htmlspecialchars($_GET[$name]);
  } else {
    return null;
  }
}

/*
 * Return JSON
 */
if (isSet($_GET['callback'])) {
    $callback = htmlspecialchars($_GET['callback']);
    header('Content-Type: application/json');
    echo ($callback . '(' . json_encode($result) . ');');
} else {
    var_dump($result);
}

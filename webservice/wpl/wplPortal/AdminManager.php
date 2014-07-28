<?php
namespace wpl\wplPortal;

use wpl\database\Database;
use wpl\model\Client;
use wpl\model\ClientUser;
use wpl\model\AdminUser;
use sdg\data\Result;
/**
 * Description of AdminManager
 *
 * @author Scott David Garson
 */

class AdminManager {
  private $db;

  public function __construct() {
      $this->db = new Database();
  }  

  /*
   * Create and Update Client
   */
  public function saveClient($name, $address, $city, $province, $postal_code, $email, $phone, $phone2, $wplEmail, $id = null) {
      $result = new Result();

      $newClient = $id == null;

      $client = new Client();
      $client->name = $name;
      $client->address = $address;
      $client->city = $city;
      $client->province = $province;
      $client->postal_code = $postal_code;
      $client->email = $email;
      $client->phone = $phone;
      $client->phone2 = $phone2;
      $client->wplEmail = $wplEmail;
      $client->guid = $newClient ? $this->db->generateGUID() : $id;

      $result->data = $client;

      if ($newClient) {
        $client->active = '1';
        $dbResult = $this->db->add('clients', $client);
        if ($dbResult) {
          $result->success = true;
          $result->message = "Client added.";
          $result->code = 200;
        } else {
          $result->success = false;
          $result->code = 304;
          $result->message = "Error adding client.";
        }
      } else {
        $where = array(
            'guid' => $id
        );
        $dbResult = $this->db->update('clients', $client->getUpdateModel(), $where);
        if ($dbResult) {
          $result->success = true;
          $result->message = "Client updated.";
          $result->code = 200;
        } else {
          $result->success = false;
          $result->code = 304;
          $result->message = "Error updating client.";
        }
      }

      return $result;
  }

  /*
   * List Clients
   */
  public function getClientList($startRecord, $pageSize, $active) {
      $result = new Result();

      $sql = "select count(*) from clients where active = '$active'";
      $dbResult = $this->db->db->prepare($sql);
      $dbResult->execute();
      $recordCount = $dbResult->fetchColumn();        

      $select = array('guid', 'name', 'address', 'city', 'province', 'phone', 'phone2');

      $orderBy = array('name' => 'ASC');
      $where = array('active' => $active);
      $dataset = $this->db->select('clients', $select, $orderBy, $where, $startRecord, $pageSize);

      $result->success = true;
      $result->code = 200;
      $result->data = array();
      $result->data['list'] = $dataset;
      $result->data['count'] = $recordCount;

      $result->message = 'client list';

      return $result;
  }

  /*
   * Search Clients
   */
  public function searchClients($searchString, $startRecord, $pageSize, $active) {
      $result = new Result();

      $searchQuery = " name LIKE :search OR address LIKE :search OR city LIKE :search AND active='$active'";

      $sql = 'SELECT COUNT(*) FROM clients WHERE' . $searchQuery;
      $dbResult = $this->db->db->prepare($sql);
      $dbResult->bindValue(':search', '%'.$searchString.'%');
      $dbResult->execute();
      $recordCount = $dbResult->fetchColumn();        

      $select = array('guid', 'name', 'address', 'city', 'province', 'phone', 'phone2');
      $where = array(
          'name'=>$searchString,
          'address'=>$searchString,
          'city'=>$searchString
      );
      $whereAnd = array('active'=>$active);
      $orderBy = array('name' => 'ASC');
      $dataset = $this->db->search('clients', $select, $orderBy, $where, $whereAnd, $startRecord, $pageSize);

      $result->success = true;
      $result->code = 200;
      $result->data = array();
      $result->data['list'] = $dataset;
      $result->data['count'] = $recordCount;

      $result->message = 'client search results';

      return $result;
  }

  /*
   * Client Detail
   */
  public function getClientDetail($guid) {
    $result = new Result();

    $select = array('guid', 'name', 'address', 'city', 'province', 'postal_code', 'phone', 'phone2', 'email', 'wplEmail', 'active');

    $where = array(
        'guid' => $guid
    );
    $orderBy = null;
    $dataset = $this->db->select('clients', $select, $orderBy, $where);

    $result->data = $dataset[0];

    $result->message = 'client detail';

    return $result;      
  }

  /*
   * Deactivate Client
   */
  public function setClientActive($guid, $active) {
    $result = new Result();

    $insert = array('active' => $active == '1');
    $where = array('guid' => $guid);
    
    $dbResult = $this->db->update('clients', $insert, $where);
    if ($dbResult) {
      $result->success = true;
      $result->message = $active ? "Client reactivated." : "Client deactivated.";
      $result->code = 200;
    } else {
      $result->success = false;
      $result->code = 304;
      $result->message = $active ? "Client reactivation failed." : "Client deactivation failed.";
    }    
    
    return $result;
  }
  
  /*
   * Client User
   */
  public function saveClientUser($firstName, $lastName, $email, $confirmEmail, $phone, $phone2, $companyID, $id = null) {
    $result = new Result();

    $newUser = $id == null;

    $user = new ClientUser();
    $user->first_name = $firstName;
    $user->last_name = $lastName;
    $user->email = $email;
    $user->confirmation_email = $confirmEmail;
    $user->phone = $phone;
    $user->phone2 = $phone2;
    $user->guid = $newUser ? $this->db->generateGUID() : $id;

    $result->data = $user;

    if ($newUser) {
      $clientKey = $this->db->getCompanyIDFromGUID($companyID);
      $user->client_id = $clientKey;
      
      $dbResult = $this->db->add('clientUsers', $user);
      if ($dbResult) {
        $result->success = true;
        $result->message = "Client added.";
        $result->code = 200;
      } else {
        $result->success = false;
        $result->code = 304;
        $result->message = "Error adding client.";
      }
    } else {
      $where = array(
          'guid' => $id
      );
      $dbResult = $this->db->update('clientUsers', $user->getUpdateModel(), $where);
      if ($dbResult) {
        $result->success = true;
        $result->message = "Client updated.";
        $result->code = 200;
      } else {
        $result->success = false;
        $result->code = 304;
        $result->message = "Error updating client.";
      }
    }

    return $result;
  }
  
  /*
   * List Client Users
   */
  public function getClientUserList($clientID, $active) {
      $result = new Result();
      
      $clientKey = $this->db->getCompanyIDFromGUID($clientID);

      $select = array('first_name', 'last_name', 'email', 'confirmation_email', 'phone', 'phone2', 'guid');

      $orderBy = array('last_name' => 'ASC', 'first_name' => 'ASC');
      $where = array('client_id' => $clientKey, 'active' => $active);
      $dataset = $this->db->select('clientUsers', $select, $orderBy, $where);

      $result->data = array();

      if ($dataset) {
        $result->success = true;
        $result->message = "Client User List";
        $result->code = 200;
        $result->data['list'] = $dataset;
      } else {
        $result->success = false;
        $result->code = 304;
        $result->data['list'] = array();
        $result->message = "No client users.";
      }

      return $result;
  }
  
  public function loadClientUserDetails($guid) {
    $result = new Result();
    $result->data = array();

    // get user detail
    $select = array('guid', 'first_name', 'last_name', 'email', 'confirmation_email', 'phone', 'phone2', 'active', 'password_set', 'client_id');
    $where = array(
        'guid' => $guid
    );
    $orderBy = null;
    $dataset = $this->db->select('clientUsers', $select, $orderBy, $where);
    
    $result->data['user'] = $dataset[0];

    // client info
    $select = array('guid', 'name', 'address', 'city', 'province', 'postal_code', 'phone', 'phone2', 'email', 'wplEmail', 'active');
    $where = array(
        'id' => $dataset[0]['client_id']
    );
    $orderBy = null;
    $dataset = $this->db->select('clients', $select, $orderBy, $where);
    $result->data['company'] = $dataset[0];

    $result->message = 'client user detail';

    return $result;      
  }
  
  /*
   * Deactivate Client
   */
  public function setClientUserActive($guid, $active) {
    $result = new Result();
    
    $insert = array('active' => $active == '1');
    $where = array('guid' => $guid);
    $dbResult = $this->db->update('clientUsers', $insert, $where);
    if ($dbResult) {
      $result->success = true;
      $result->message = $active ? "User reactivated." : "User deactivated.";
      $result->code = 200;
    } else {
      $result->success = false;
      $result->code = 304;
      $result->message = $active ? "User reactivation failed." : "User deactivation failed.";
    }    
    
    return $result;
  }
  
  /*
   * Admin Users
   */
  public function saveAdminUser($username, $email, $password, $id = null) {
    $result = new Result();

    $newUser = $id == null;
    
    $user = new AdminUser();
    $user->username = $username;
    $user->email = $email;
    $user->temp_password = $this->db->generateTempPassword();
    $user->password = md5($user->temp_password);
    $user->guid = $newUser ? $this->db->generateGUID() : $id;

    $result->data = $user;

    if ($newUser) {
      $dbResult = $this->db->add('adminUsers', $user);
      if ($dbResult) {
        $result->success = true;
        $result->message = "Admin user added.";
        $result->code = 200;
      } else {
        $result->success = false;
        $result->code = 304;
        $result->message = "Error adding admin user.";
      }
    } else {
      $where = array(
          'guid' => $id
      );
      $dbResult = $this->db->update('clientUsers', $user->getUpdateModel(), $where);
      if ($dbResult) {
        $result->success = true;
        $result->message = "Client updated.";
        $result->code = 200;
      } else {
        $result->success = false;
        $result->code = 304;
        $result->message = "Error updating client.";
      }
    }
    
    // TODO: Email user temporary password and link to change password

    return $result;
  }  
  
  public function changeAdminPassword($id, $oldPassword, $password) {
    $result = new Result();
    
    $user = array(
     'password' => $password,
     'password_set' => '1',
     'temp_password' => ''
    );
    
    $where = array(
     'guid' => $id,
     'password' => $oldPassword
    );
    
    $dbResult = $this->db->update('adminUsers', $user, $where);
    if ($dbResult) {
      $result->success = true;
      $result->message = "Admin password updated.";
      $result->code = 200;
    } else {
      $result->success = false;
      $result->code = 304;
      $result->message = "Error updating admin password.";
    }    

    return $result;
  }
  
  public function getAdminUserList($active) {
    $result = new Result();

    $select = array('username', 'email', 'guid');

    $orderBy = array('email' => 'ASC');
    $where = array('active' => $active);
    $dataset = $this->db->select('adminUsers', $select, $orderBy, $where);

    $result->data = array();

    if ($dataset) {
      $result->success = true;
      $result->message = "Admin User List";
      $result->code = 200;
      $result->data['list'] = $dataset;
    } else {
      $result->success = false;
      $result->code = 304;
      $result->data['list'] = array();
      $result->message = "No admin users.";
    }

    return $result;
  }  
  
  public function getAdminUserDetail($id) {
    $result = new Result();
    $result->data = array();

    // get user detail
    $select = array('guid', 'username', 'email');
    $where = array(
        'guid' => $id
    );
    $orderBy = null;
    $dataset = $this->db->select('adminUsers', $select, $orderBy, $where);

    $result->success = true;
    $result->code = 200;
    $result->data = $dataset[0];
    $result->message = 'admin user detail';

    return $result;     
    
  }
  
  /*
   * Deactivate
   */
  public function setAdminUserActive($guid, $active) {
    $result = new Result();
    
    $insert = array('active' => $active == '1');
    $where = array('guid' => $guid);
    
    $dbResult = $this->db->update('adminUsers', $insert, $where);
    if ($dbResult) {
      $result->success = true;
      $result->message = $active ? "Admin user reactivated." : "Admin user deactivated.";
      $result->code = 200;
    } else {
      $result->success = false;
      $result->code = 304;
      $result->message = $active ? "Admin user reactivation failed." : "Admin user deactivation failed.";
    }    
    
    return $result;
  }  
  
  public function login($username, $password) {
    $result = new Result();
    $result->data = array();

    // get user detail
    $select = array('guid');
    $where = array(
        'username' => $username,
        'password' => $password
    );
    $orderBy = null;
    $dataset = $this->db->select('adminUsers', $select, $orderBy, $where);

    if ($dataset) {
      $result->success = true;
      $result->code = 200;
      $result->data = $dataset[0];
      $result->message = 'login success';
    } else {
      $result->success = false;
      $result->code = 304;
      $result->data = null;
      $result->message = 'login failed';
    }
    

    return $result;    
  }
  
}

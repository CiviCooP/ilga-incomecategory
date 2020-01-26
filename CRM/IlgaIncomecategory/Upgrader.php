<?php
use CRM_IlgaIncomecategory_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_IlgaIncomecategory_Upgrader extends CRM_IlgaIncomecategory_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run SQL scripts that must be installed.
   */
  public function install() {
    $this->executeSqlFile('sql/ilga_income_worldbank.sql');
    $this->executeSqlFile('sql/ilga_iso_translation.sql');
  }

  /**
   * Example: Work with entities usually not available during the install step.
   *
   * This method can be used for any post-install tasks. For example, if a step
   * of your installation depends on accessing an entity that is itself
   * created during the installation (e.g., a setting or a managed entity), do
   * so here to avoid order of operation problems.
   *
  public function postInstall() {
    $customFieldId = civicrm_api3('CustomField', 'getvalue', array(
      'return' => array("id"),
      'name' => "customFieldCreatedViaManagedHook",
    ));
    civicrm_api3('Setting', 'create', array(
      'myWeirdFieldSetting' => array('id' => $customFieldId, 'weirdness' => 1),
    ));
  }

  /**
   * Example: Removes the income tables
   */
  public function uninstall() {
    CRM_Core_DAO::executeQuery('drop table if exists ilga_iso_translation');
    CRM_Core_DAO::executeQuery('drop table if exists ilga_income_worldbank');
  }

  /**
   * Example: Run a simple query when a module is enabled.
   */
  public function enable() {
    $this->createJob('ILGA Income Category','Adds World Bank Income to Organizations','IncomeCategory');
    $this->createGroup('high_income','High Income Invoice','Used to send organisations invoices according to there income');
    $this->createGroup('low_income','Low Income Invoice','Used to send organisations invoices according to there income');
    $this->createGroup('unknown_income','Unknown Income Invoice','Used to send organisations invoices according to there income');
  }

  /**
   * Example: Run a simple query when a module is disabled.
   */
  public function disable() {
    $this->removeJob('ILGA Income Category');
  }

  private function createJob($name,$description,$action) {
    $apiParams = [
      'name' => $name,
      'description' => $description,
      'api_entity' => 'Job',
      'run_frequency' => 'Daily',
      'api_action' => $action,
      'is_active'  => 0,
      'parameters' => '',
    ];
    $jobId = CRM_Core_DAO::singleValueQuery('select id from civicrm_job where name=%1',[
        1 => [$apiParams['name'],'String']
      ]
    );
    if($jobId){
      $apiParams['id'] = $jobId;
    }
    civicrm_api3('Job', 'create', $apiParams);
  }

  private function removeJob($name) {
    $jobId = CRM_Core_DAO::singleValueQuery('select id from civicrm_job where name=%1',[
        1 => [$name,'String']
      ]
    );
    if($jobId){
      $apiParams['id'] = $jobId;
      civicrm_api3('Job', 'delete', $apiParams);
    }
  }

  private function createGroup($name,$title,$description) {
    try {
      civicrm_api3('Group', 'create', [
        'name' =>$name,
        'title' => $title,
        'description' => $description,
        'is_active' => 1,
        'group_type' => 'Mailing List',
      ]);
    } catch (CiviCRM_API3_Exception $ex) {
      // in case it already exists - just ignore
    }
    return TRUE;
  }

}

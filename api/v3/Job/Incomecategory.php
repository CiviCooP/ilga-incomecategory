<?php
/**
 * @author Klaas Eikelboom  <klaas.eikelboom@civicoop.org>
 * @date 26-Jan-2020
 * @license  AGPL-3.0
 */
use CRM_IlgaIncomecategory_ExtensionUtil as E;

/**
 * Job.IncomeCategory API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_job_IncomeCategory_spec(&$spec) {
}

/**
 * Job.IncomeCategory API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_job_Incomecategory($params) {
  try{
    $returnValues = [];
    $categorizer = new CRM_IlgaIncomecategory_Categorizer();
    $categorizer->process();
    return civicrm_api3_create_success($returnValues, $params, 'Job', 'IncomeCategory');
  } catch (Exception $ex){
      throw new API_Exception($ex, 'incomecategory_failed');
  }
}

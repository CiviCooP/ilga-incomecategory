<?php
/**
 * @author Klaas Eikelboom  <klaas.eikelboom@civicoop.org>
 * @date 26-Jan-2020
 * @license  AGPL-3.0
 */
class CRM_IlgaIncomecategory_Categorizer
{
   var $highIncomeGroupId;
   var $lowIncomeGroupId;
   var $unknownIncomeGroupId;
   var $year;
   var $map;

  /**
   * CRM_IlgaIncomecategory_Categorizer constructor.
   */
  public function __construct($year = '2020')
  {
    $this->highIncomeGroupId = civicrm_api3('Group', 'getvalue', [
        'name' => 'high_income',
        'return' => 'id'
      ]
    );
    $this->lowIncomeGroupId = civicrm_api3('Group', 'getvalue', [
        'name' => 'low_income',
        'return' => 'id'
      ]
    );
    $this->unknownIncomeGroupId = civicrm_api3('Group', 'getvalue', [
        'name' => 'unknown_income',
        'return' => 'id'
      ]
    );
    $this->year=$year;
    $this->map['Low income'] = $this->lowIncomeGroupId;
    $this->map['Upper middle income'] =  $this->lowIncomeGroupId;
    $this->map['High income']= $this->highIncomeGroupId;
    $this->map['Lower middle income'] =   $this->lowIncomeGroupId;
  }


  public function map($countryId){
    $sql = <<< SQL
    select income_category from ilga_income_worldbank  iwb
    join   ilga_iso_translation iit on iwb.iso3 = iit.iso3
    join   civicrm_country cnt on (iit.iso2=cnt.iso_code)
    where  cnt.id = %1
    and    year=%2
SQL;
    $incomeCategory = CRM_Core_DAO::singleValueQuery($sql,[
        1 =>[$countryId,'Integer'],
        2 =>[$this->year,'String']
      ]);
    if($incomeCategory){
       return $this->map[$incomeCategory];
    } else {
      return $this->unknownIncomeGroupId;
    }
  }


  public function update($contactId,$updateGroupId){
      foreach([$this->lowIncomeGroupId,$this->highIncomeGroupId,$this->unknownIncomeGroupId] as $groupId){
        $gcId = CRM_Core_DAO::singleValueQuery('select id from civicrm_group_contact where contact_id=%1 and group_id=%2',[
          1 => [$contactId,'Integer'],
          2 => [$groupId,'Integer']
         ]);
        if($gcId && $groupId!=$updateGroupId){
          // found a record but not the update record so remove it
          CRM_Core_DAO::executeQuery('delete from civicrm_group_contact where id=%1',[
             1 => [$gcId, 'Integer']
          ]);
        }
        if($gcId && $groupId==$updateGroupId){
          // found and its the update record, so we are ready
        }
        if(!$gcId && $groupId!=$updateGroupId){
          // found no record but not the update so we are ready
        }
        if(!$gcId && $groupId==$updateGroupId){
          civicrm_api3('GroupContact','create',['contact_id'=>$contactId,'group_id'=>$updateGroupId]);
        }
      }
  }

  public function process(){
    set_time_limit(0);
    $sql = <<< SQL
    select contact_id, country_id
    from civicrm_address a
    join civicrm_contact c on (c.id = a.contact_id)
    where a.is_primary = 1
    and c.contact_type = 'Organization'
    and a.country_id is not null
    and contact_id=6820
SQL;
   $dao = CRM_Core_DAO::executeQuery($sql);
   while($dao->fetch()){
      $groupId = $this->map($dao->country_id);
      $this->update($dao->contact_id,$groupId);

   }
  }


}

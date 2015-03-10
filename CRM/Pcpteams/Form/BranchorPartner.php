<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Pcpteams_Form_BranchorPartner extends CRM_Core_Form {
  function buildQuickForm() {

    // add form elements
    $this->addEntityRef('pcp_team_contact', ts('Branch or Corporate Patner'), array('api' => array('params' => array('contact_type' => 'Organization')), 'create' => TRUE), TRUE);
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    $branchOrPartnerID = $values['pcp_team_contact'];
    $pcpId = CRM_Pcpteams_Constant::C_PCP_ID;
    // Get custom org ID
    $customResult = CRM_Pcpteams_Utils::checkOrUpdateUserPcpGroup($pcpId, 'get');
    if($customResult['is_error'] == 0  && !empty($branchOrPartnerID)) {
      // Create/Update Org ID with the user selected organization
      CRM_Pcpteams_Utils::checkOrUpdateUserPcpGroup($pcpId, 'create', array('value' =>  $branchOrPartnerID, 'id' => 1));
    }
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}

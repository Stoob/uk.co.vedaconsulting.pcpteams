<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Pcpteams_Form_TeamConfirm extends CRM_Core_Form {
  function preProcess(){
    $this->assign('teamTitle', $this->get('teamName'));
    CRM_Utils_System::setTitle(ts('Team Name Available'));

    $this->_pcpId = $this->controller->get('pcpId');
    //$this->_pcpId = CRM_Utils_Request::retrieve('id', 'Positive');
    $userId = CRM_Pcpteams_Utils::getloggedInUserId();
    if (!$this->_pcpId) {
     $this->_pcpId =  CRM_Pcpteams_Utils::getPcpIdByUserId($userId);
    }
    parent::preProcess();
  }
  function buildQuickForm() {

    $this->add('textarea', 'description', ts('Email Addresses') , '');
    $this->addButtons(array(
      array(
        'type' => 'next',
        'name' => ts('Next'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    //return TRUE;
    $values = $this->controller->exportValues($this->_name); 
    $emails = array_map('trim', explode(',', $values['description']));
    $userId = CRM_Pcpteams_Utils::getloggedInUserId();
    // Find the msg_tpl ID of sample invite template
    $result = civicrm_api3('MessageTemplate', 'get', array( 'sequential' => 1, 'version'=> 3, 'msg_title' => "Sample Team Invite Template",));
    if(!civicrm_error($result) && $result['id']) {
      // Send Invitation emails
      CRM_Pcpteams_Utils::sendInviteEmail($result['id'], $userId, $emails);
    }
    
    // Create Team Invite activity
    CRM_Pcpteams_Utils::createPcpActivity($userId, CRM_Pcpteams_Constant::C_CF_TEAM_INVITE, 'Invited to '.$this->teamTitle, 'PCP Team Invite');
    
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

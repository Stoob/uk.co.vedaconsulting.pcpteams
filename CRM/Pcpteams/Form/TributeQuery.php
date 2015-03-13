<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Pcpteams_Form_TributeQuery extends CRM_Core_Form {
  function preProcess(){
    CRM_Utils_System::setTitle(ts('Reason'));
    parent::preProcess();  
  }
  
  function buildQuickForm() {
    
    $teamOptions = array();
    $teamOptions = array(
        ts(' I just want to support you'),
        ts(' Iam doing this in memory of someone'),
        ts(' Iam doing this in celebration of an event')
      );
    $this->addRadio('teamOption', '', $teamOptions, NULL, '<br/><br/>');
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
    $values = $this->exportValues();
    $this->set("workflowTribute", $values['teamOption']);
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}

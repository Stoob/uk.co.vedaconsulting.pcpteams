<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Pcpteams_Form_PCP_InlineProfilePic extends CRM_Core_Form {
  function prepProcess(){
    $this->_pcpId             = CRM_Utils_Request::retrieve('id', 'Positive');
    $this->component_page_id  = CRM_Utils_Request::retrieve('pageId', 'Positive');
    parent::preProcess();
  }
  
  function buildQuickForm() {

    // add form elements
    $this->addElement('file', 'image_URL', ts('Browse/Upload Image'), 'size=30 maxlength=60');
    $this->addUploadElement('image_URL');
    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => ts('Upload'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $params = $this->controller->exportValues($this->_name);
    CRM_Core_BAO_File::formatAttachment($params, $params, 'civicrm_pcp', $this->_pcpId);
    CRM_Core_BAO_File::processAttachment($params, 'civicrm_pcp', $this->_pcpId);
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
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
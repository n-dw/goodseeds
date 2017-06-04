<?php
namespace Craft;

class FormBuilder2_FormController extends BaseController
{

  protected $allowAnonymous = true;


  /**
   * Get All Forms
   *
   */
  public function actionFormsIndex()
  { 

    $formItems = craft()->formBuilder2_form->getAllForms();
    $settings = craft()->plugins->getPlugin('FormBuilder2')->getSettings();
    $plugins = craft()->plugins->getPlugin('FormBuilder2');

    $variables['title']       = 'FormBuilder2';
    $variables['formItems']   = $formItems;
    $variables['settings']    = $settings;
    $variables['plugin']      = $plugins;
    $variables['navigation']  = $this->navigation();

    return $this->renderTemplate('formbuilder2/forms/index', $variables);
  }


  /**
   * View/Edit Form
   *
   */
  public function actionEditForm(array $variables = array())
  {
    $variables['brandNewForm'] = false;
    $variables['navigation']  = $this->navigation();

    if (!empty($variables['formId'])) {
      if (empty($variables['form'])) {
        $variables['form'] = craft()->formBuilder2_form->getFormById($variables['formId']);

        if (!$variables['form']) { 
          throw new HttpException(404);
        }
      }
      $variables['title'] = $variables['form']->name;
    } else {
      if (empty($variables['form'])) {
        $variables['form'] = new FormBuilder2_FormModel();
        $variables['brandNewForm'] = true;
      }
      $variables['title'] = Craft::t('Create a new form');
    }

    $this->renderTemplate('formbuilder2/forms/_edit', $variables);
  }

  /**
   * Saves New Form.
   *
   */
  public function actionSaveForm()
  {
    $this->requirePostRequest();
    $form = new FormBuilder2_FormModel();

    $form->id                           = craft()->request->getPost('formId');
    $form->name                         = craft()->request->getPost('name');
    $form->handle                       = craft()->request->getPost('handle');
    $form->fieldLayoutId                = craft()->request->getPost('fieldLayoutId');
    $form->formSettings                 = craft()->request->getPost('formSettings');
    $form->spamProtectionSettings       = craft()->request->getPost('spamProtectionSettings');
    $form->messageSettings              = craft()->request->getPost('messageSettings');
    $form->notificationSettings         = craft()->request->getPost('notificationSettings');

    $fieldLayout = craft()->fields->assembleLayoutFromPost();
    $fieldLayout->type = ElementType::Asset;
    $form->setFieldLayout($fieldLayout);

    if (craft()->formBuilder2_form->saveForm($form)) {
      craft()->userSession->setNotice(Craft::t('Form saved.'));
      $this->redirectToPostedUrl($form);
    } else {
      craft()->userSession->setError(Craft::t('Couldn’t save form.'));
    }

    craft()->urlManager->setRouteVariables(array(
      'form' => $form
    ));
  }

  /**
   * Delete Form.
   *
   */
  public function actionDeleteForm()
  {
    $this->requirePostRequest();
    $this->requireAjaxRequest();

    $formId = craft()->request->getRequiredPost('id');

    craft()->formBuilder2_form->deleteFormById($formId);
    $this->returnJson(array('success' => true));
  }

  /**
   * Sidebar Navigation
   *
   */
  public function navigation()
  {
    $navigationSections = array(
      array(
        'heading' => Craft::t('Menu'),
        'nav'     => array(
          array(
            'label' => Craft::t('Dashboard'),
            'icon'  => 'tachometer',
            'extra' => '',
            'url'   => UrlHelper::getCpUrl('formbuilder2'),
          ),
          array(
            'label' => Craft::t('Forms'),
            'icon'  => 'list-alt',
            'extra' => craft()->formBuilder2_form->getTotalForms(),
            'url'   => UrlHelper::getCpUrl('formbuilder2/forms'),
          ),
          array(
            'label' => Craft::t('Entries'),
            'icon'  => 'file-text-o',
            'extra' => craft()->formBuilder2_entry->getTotalEntries(),
            'url'   => UrlHelper::getCpUrl('formbuilder2/entries'),
          ),
        )
      ),
      array(
        'heading' => Craft::t('Quick Links'),
        'nav'     => array(
          array(
            'label' => Craft::t('Create New Form'),
            'icon'  => 'pencil-square-o',
            'extra' => '',
            'url'   => UrlHelper::getCpUrl('formbuilder2/forms/new'),
          ),
        )
      ),
      array(
        'heading' => Craft::t('Tools'),
        'nav'     => array(
          array(
            'label' => Craft::t('Export'),
            'icon'  => 'share-square-o',
            'extra' => '',
            'url'   => UrlHelper::getCpUrl('formbuilder2/tools/export'),
          ),
          array(
            'label' => Craft::t('Configuration'),
            'icon'  => 'sliders',
            'extra' => '',
            'url'   => UrlHelper::getCpUrl('formbuilder2/tools/configuration'),
          ),
        )
      ),
    );
    return $navigationSections;
  }
}

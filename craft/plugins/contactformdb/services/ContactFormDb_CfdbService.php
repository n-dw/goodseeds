<?php
/**
 * Contact Form DB plugin for Craft CMS
 *
 * ContactFormDb_Cfdb Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      natedewaard.com
 * @package   ContactFormDb
 * @since     1.0.0
 */

namespace Craft;

class ContactFormDb_CfdbService extends BaseApplicationComponent
{
    /**
     * This function can literally be anything you want, and you can have as many service functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     craft()->contactFormDb_cfdb->exampleService()
     */
    public function saveContactFormSubmission($submission)
    {
        //check if we have the submission, in case the email is sent multiple times or form submitted.
        $record = ContactFormDb_CfdbRecord::model()->findByAttributes(
            array(
                'email' => $submission['email'],
                'name' => $submission['name'],
                'inquiryType' => $submission['inquiryType'],
                'message' => $submission['message'],
            )
        );

        if(!$record){
            $record = new ContactFormDb_CfdbRecord();

            $fields = ['name', 'email', 'inquiryType', 'message'];
            foreach ($fields as $field)
            {
                $record->$field = $submission[$field];
            }

            $record->validate();

            $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
            try
            {
                if (!$record->getErrors())
                {
                    $record->save(false);

                    if ($transaction !== null)
                    {
                        $transaction->commit();
                    }

                    return true;
                }
            } catch (\Exception $e)
            {
                if ($transaction !== null)
                {
                    $transaction->rollback();
                }
                throw $e;
            }

            if ($transaction !== null)
            {
                $transaction->rollback();
            }

            return false;
        }
    }
}
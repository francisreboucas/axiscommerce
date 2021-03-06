<?php
/**
 * Axis
 * 
 * This file is part of Axis.
 * 
 * Axis is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Axis is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Axis.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category    Axis
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Controller
 * @copyright   Copyright 2008-2011 Axis
 * @license     GNU Public License V3.0
 */

/**
 * 
 * @category    Axis
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Controller
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Admin_Locale_LanguageController extends Axis_Admin_Controller_Back
{
    public function indexAction()
    {
        $this->view->pageTitle = Axis::translate('locale')->__('Languages');
        $this->render();
    }

    public function listAction()
    {
        $this->_helper->layout->disableLayout();

        $dbField = new Axis_Filter_DbField();
        $order = $dbField->filter($this->_getParam('sort', 'id')) . ' '
               . $dbField->filter($this->_getParam('dir', 'ASC'));

        $limit = (int) $this->_getParam('limit', 20);
        $start = $this->_getParam('start', 0);

        $select = Axis::single('locale/language')
            ->select()
            ->calcFoundRows()
            ->order($order)
            ->limit($limit, $start)
            ;

        return $this->_helper->json
            ->setData($select->fetchAll())
            ->setCount($select->count())
            ->sendSuccess();
    }

    public function saveAction()
    {
        $this->_helper->layout->disableLayout();
        
        $language = $this->_getParam('language');
        $locale = $this->_getParam('locale_code');
        $id = $this->_getParam('id');
        
        if (!strstr($locale, '_')) {
            Axis::message()->addError(
                Axis::translate('locale')->__(
                    'Locale format is unsupported'
            ));
            return $this->_helper->json->sendFailure();
        }
        $code = current(explode('_', $locale));
        
        if (is_numeric($id)) {
            $row = Axis::single('locale/language')->find($id)->current();
        } else {
            $row = Axis::single('locale/language')->createRow();
        }
        
        $row->setFromArray(array(
            'code' => $code,
            'language' => $language,
            'locale' => $locale
        ));
        
        $row->save();

        Axis::message()->addSuccess(
            Axis::translate('locale')->__(
                'Language was saved successfully'
        ));
        return $this->_helper->json->sendSuccess();
    }
    
    public function deleteAction()
    {
        $this->getHelper('layout')->disableLayout();
        $ids = Zend_Json_Decoder::decode($this->_getParam('data'));
        
        if (!count($ids)) {
            Axis::message()->addError(
                Axis::translate('locale')->__(
                    'No language found to delete'
            ));
            return $this->_helper->json->sendFailure();
        }
        Axis::single('locale/language')->delete($this->db->quoteInto('id IN(?)', $ids));
        
        Axis::dispatch('locale_language_delete', $ids);

        Axis::message()->addSuccess(
            Axis::translate('locale')->__(
                'Language was deleted successfully'
        ));
        return $this->_helper->json->sendSuccess();
    }
    
    /**
     * Change the locale
     * 
     */
    public function changeAction()
    {
        $locale = $this->_getParam('new_locale');
        
        if ($locale) {
            Axis_Locale::setLocale($locale);
        }
        
        $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
    }
}
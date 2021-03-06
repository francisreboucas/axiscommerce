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
class Axis_Admin_UsersController extends Axis_Admin_Controller_Back
{
    public function indexAction()
    {
        $this->view->pageTitle = Axis::translate('admin')->__('Administrators');
        $this->view->roles = Axis::single('admin/acl_role')
            ->select(array('id', 'role_name'))
            ->fetchPairs();
        $this->render();
    }

    public function getListAction()
    {
        $select = Axis::single('admin/user')->select()
            ->calcFoundRows()
            ->addFilters($this->_getParam('filter', array()))
            ->limit(
                $this->_getParam('limit', 25),
                $this->_getParam('start', 0)
            )
            ->order(
                $this->_getParam('sort', 'id')
                . ' '
                . $this->_getParam('dir', 'DESC')
            );

        $data = $select->fetchAll();

        foreach ($data as &$row) {
            $row['password'] = '';
        }
        return $this->_helper->json
            ->setData($data)
            ->setCount($select->count())
            ->sendSuccess();
    }

    public function saveAction()
    {
        $this->_helper->layout->disableLayout();
        $dataset = Zend_Json::decode($this->_getParam('data'));
        if (!sizeof($dataset)) {
            Axis::message()->addError(
                Axis::translate('core')->__(
                    'No data to save'
                )
            );
            return $this->_helper->json->sendFailure();
        }
        $model = Axis::model('admin/user');
        foreach ($dataset as $_row) {
            if (!empty($_row['password'])) {
                $_row['password'] = md5($_row['password']);
            } else {
                unset($_row['password']);
            }
            $row = $model->getRow($_row);
            $row->is_active = false;
            $row->is_active = !empty($_row['is_active']);
            
            $row->save();
        }

        Axis::message()->addSuccess(
            Axis::translate('admin')->__(
                'User was saved successfully'
            )
        );
        return $this->_helper->json->sendSuccess();
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();

        $data = Zend_Json::decode($this->_getParam('data'));

        if (!count($data)) {
            return;
        }

        if (in_array(Zend_Auth::getInstance()->getIdentity(), $data)) {
            Axis::message()->addError(
                Axis::translate('admin')->__(
                    'You cannot delete yourself'
                )
            );
            return $this->_helper->json->sendFailure();
        }

        Axis::single('admin/user')->delete(
            $this->db->quoteInto('id IN(?)', $data)
        );
        Axis::message()->addSuccess(
            Axis::translate('admin')->__(
                'User was deleted successfully'
            )
        );
        return $this->_helper->json->sendSuccess();
    }

    public function getRolesAction()
    {
        $roles = array_values(
            Axis::single('admin/acl_role')->fetchAll()->toArray()
        );
        return $this->_helper->json
            ->setData($roles)
            ->sendSuccess();
    }
}
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
 * @subpackage  Axis_Admin_Model
 * @copyright   Copyright 2008-2011 Axis
 * @license     GNU Public License V3.0
 */

/**
 * 
 * @category    Axis
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Model
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Admin_Model_User extends Axis_Db_Table 
{
    /**
     * The default table name 
     */
    protected $_name = 'admin_user';
    
    public function getRole($id)
    {
        return $this->select('role_id')->where('id = ?', $id)->fetchOne();
    }
      
    public function getCount()
    {
        return parent::count(null, 'COUNT(id)');
    }
    
    public function getExist(array $ids = array()) 
    {
        $select = $this->select('id');
        if (count($ids)) {
            $select->where('id IN(?)', $ids);
        }
        return $select->fetchCol();
    }
    
    public function update(array $data, $where)
    {
        if (empty($data['modified'])) {
            $data['modified'] = Axis_Date::now()->toSQLString();
        }
        return parent::update($data, $where);
    }
    
    public function insert(array $data)
    {
        if (empty($data['created'])) {
            $data['created'] = Axis_Date::now()->toSQLString();
        }
        return parent::insert($data);
    }
}

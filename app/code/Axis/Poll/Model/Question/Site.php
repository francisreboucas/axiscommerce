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
 * @package     Axis_Poll
 * @subpackage  Axis_Poll_Model
 * @copyright   Copyright 2008-2011 Axis
 * @license     GNU Public License V3.0
 */

/**
 * 
 * @category    Axis
 * @package     Axis_Poll
 * @subpackage  Axis_Poll_Model
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Poll_Model_Question_Site extends Axis_Db_Table
{
    protected $_name = 'poll_question_site';
    
    public function getSitesNamesAssigns()
    {
        $rows = $this->select('question_id')
            ->joinLeft('core_site', 
                'cs.id = pqs.site_id',
                array('id', 'name')
            )
            ->fetchAll()
            ;
        $dataset = array();
        foreach ($rows as $row) {
        	$dataset[$row['question_id']][$row['id']] = $row['name'];
        }
        return $dataset;
    }
    
    public function getSitesIds($questionId)
    {
        return $this->select('site_id')
            ->where('question_id = ?', $questionId)
            ->fetchCol();
    }
}
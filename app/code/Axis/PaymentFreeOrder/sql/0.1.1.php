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
 * @package     Axis_PaymentFreeOrder
 * @copyright   Copyright 2008-2011 Axis
 * @license     GNU Public License V3.0
 */

class Axis_PaymentFreeOrder_Upgrade_0_1_1 extends Axis_Core_Model_Migration_Abstract
{
    protected $_version = '0.1.1';
    protected $_info = 'Filters by max and min order totals added';

    public function up()
    {
        $installer = Axis::single('install/installer');

        Axis::single('core/config_field')
            ->add('payment/FreeOrder_Standard/minOrderTotal', 'Minimum order total amount', '', 'string', array('translation_module' => 'Axis_Admin'))
            ->add('payment/FreeOrder_Standard/maxOrderTotal', 'Maximum order total amount', '', 'string', array('translation_module' => 'Axis_Admin'));
    }

    public function down()
    {
    }
}
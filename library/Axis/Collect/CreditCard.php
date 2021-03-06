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
 * @package     Axis_Collect
 * @copyright   Copyright 2008-2011 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_Collect
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Collect_CreditCard implements Axis_Collect_Interface
{
    /**
     * @static
     * @var const array
     */
    static protected $_cards = array(
//        'ALL'              => Zend_Validate_CreditCard::ALL,
        'AMERICAN_EXPRESS' => Zend_Validate_CreditCard::AMERICAN_EXPRESS,
        'UNIONPAY'         => Zend_Validate_CreditCard::UNIONPAY,
        'DINERS_CLUB'      => Zend_Validate_CreditCard::DINERS_CLUB,
        'DINERS_CLUB_US'   => Zend_Validate_CreditCard::DINERS_CLUB_US,
        'DISCOVER'         => Zend_Validate_CreditCard::DISCOVER,
        'JCB'              => Zend_Validate_CreditCard::JCB,
        'LASER'            => Zend_Validate_CreditCard::LASER,
        'MAESTRO'          => Zend_Validate_CreditCard::MAESTRO,
        'MASTERCARD'       => Zend_Validate_CreditCard::MASTERCARD,
        'SOLO'             => Zend_Validate_CreditCard::SOLO,
        'VISA'             => Zend_Validate_CreditCard::VISA,

    );

    /**
     *
     * @static
     * @return array
     */
    public static function collect()
    {
        return self::$_cards;
    }

    /**
     *
     * @static
     * @param string $id
     * @return string
     */
    public static function getName($id)
    {
        if (!$id) {
            return '';
        }
        if (strstr($id, ",")) {
            $ret = array();
            foreach(explode(",", $id) as $key) {
                if (array_key_exists($key, self::$_cards))
                    $ret[$key] = self::$_cards[$key];
            }
            return implode(", ", $ret);
        }
        return self::$_cards[$id];
    }
}
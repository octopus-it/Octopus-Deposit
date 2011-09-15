<?php

/**
 * Octopus-IT Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   OCTOPUS
 * @package    Octopus_Deposit
 * @copyright  Copyright (c) 2010-2011 Octopus-IT. (http://www.octopus-it.com)
 * @license    http://www.octopus-it.com/license.txr
 */
$installer = $this;

/* $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$setup->addAttribute('catalog_product', 'octopus_deposit', array(
       			'is_html_allowed_on_front'=> false,
				'backend'       => '',
				'source'        => '',
				'entity_model'	=> 'catalog/product',
				'label'         => 'Deposit',
				'group'			=> 'Prices',
				'input'         => 'text',
				'type'			=> 'decimal',
				'is_html_allowed_on_front' => false,
				'global'        => true,
				'visible'       => true,
				'required'      => false,
				'user_defined'  => false,
				'default'       => '',
				'visible_on_front' => true,
                  'is_global' => true,
                  'is_visible' => true,
                  'is_visible_on_front' => true
    ));

$installer->endSetup();
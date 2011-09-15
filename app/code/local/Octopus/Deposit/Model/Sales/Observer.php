<?php
    /**
     * Gets the sales_quote_items and adjusts the custom price - changing it to the deposit price
     * @param   Varien_Event_Observer $observer
     * @return  Octopus_Agency_Model_Sales_Observer
     */


class Octopus_Deposit_Model_Sales_Observer
{
    public function __construct()
    {
    }
	
	// Get a deposit value and place it into the custom price of a quote in the cart	
	public function sales_quote_item_booking($observer)
	{
		//// Get the data sent to the listener
		$event = $observer->getEvent();
		$quoteItem = $event->getQuoteItem(); 
		//$_quote = $event->getQuote();
		
		// Find the product ID in the quote_item
		$productId = $quoteItem->getProductId();
		
		// Find the deposit value using the ProductId
		$octopusDeposit = Mage::getModel('catalog/product')
                            ->load($productId)
                            ->getOctopusDeposit();
		
		// If there is a depoist value, use it
		if($octopusDeposit > 0) {
			$quoteItem->setCustomPrice($octopusDeposit);
			$quoteItem->setOriginalCustomPrice($octopusDeposit);
			$quoteItem->getProduct()->setIsSuperMode(true);
			$quoteItem->save();
		}
		
		return true;
	}
}
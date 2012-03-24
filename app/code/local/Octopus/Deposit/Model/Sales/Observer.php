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
	
	// Work out deposit value, update the price, and Put remaining qty in field in quote_item table	
	public function sales_quote_item_deposit($observer)
	{
		//// Get the data sent to the listener
    	$event = $observer->getEvent();
		$quoteItem = $event->getQuoteItem(); 
		
		// Find the product ID in the quote_item
		$productId = $quoteItem->getProductId();
		
		// Find the deposit value using the ProductId
		$product = Mage::getModel('catalog/product')
                            ->load($productId);
							
		$octopusDeposit = $product->getOctopusDeposit();							
		$octopusDepositIspercentage = $product->getOctopusDepositIspercentage();
		$productPrice = $quoteItem->getProduct()->getPrice();
		
		// If there is a depoist value, use it
		if($octopusDeposit > 0) {
			
			if($octopusDepositIspercentage == 0) { // Use absolute price
				$octopusDepositPrice = $octopusDeposit;
			} 
			if($octopusDepositIspercentage == 1) { // use percentage deposit
				$octopusDepositPrice = $productPrice * ($octopusDeposit / 100);
			}
			
			//Work out remaining - this is the QTY x Original Price, so that any amount can be paid back
			$octopusDepositRemain = $quoteItem->getProduct()->getQty() * ($productPrice - $octopusDepositPrice);
			
			//set prices
			$quoteItem->setOctopusDepositRemain($octopusDepositRemain);
			$quoteItem->setCustomPrice($octopusDepositPrice);
			$quoteItem->setOriginalCustomPrice($octopusDepositPrice);
			$quoteItem->getProduct()->setIsSuperMode(true);
		}
		
		return $this;
	}
	
	// Put remaining qty in field in order_item table	
	public function sales_convert_quote_item_to_order_item_deposit($observer)
	{
		$event = $observer->getEvent();
		
		$orderItem = $event->getOrderItem();	
		$item = $event->getItem();	
		
		$orderItem->setOctopusDepositRemain($item->getOctopusDepositRemain());
		
		return $this;
	}
	
	// When order paid then update the status to say "Deposit Paid"
	public function sales_order_status_after_deposit($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$state = $observer->getEvent()->getState();
		
		if($state == Mage_Sales_Model_Order::STATE_COMPLETE) {
		
			$items = $order->getAllItems();
			
			foreach($items as $item) {
				if($item->getOctopusDepositRemain() > 0) {
					$order->setStatus('octopus_deposit');
					break;
				}
			} // End Foreach			
		} //  End if state = completed

		return $this;
	}
	
	public function sales_order_invoice_register_deposit($observer)
	{
		$order = $observer->getEvent()->getOrder();
		//echo 'sales_order_invoice_register_deposit';
		
		//$order->setTotalInvoiced(20.000);
		
		//echo $order->setBaseGrandTotal('20.000');
		
		//var_dump($order);
		//die();
	}
	
	public function sales_order_invoice_pay_deposit($observer)
	{
		//$event = $observer->getEvent();
		//echo 'sales_order_invoice_pay_deposit';
		//var_dump($event);
		
		$invoice = $observer->getEvent ()->getInvoice ();
		
		//echo $invoice->getOrder()->getBaseTotalPaid();
		
		if(Mage::getStoreConfig('octopus/deposit/shopping_cart_deposit')=='' or Mage::getStoreConfig('octopus/deposit/shopping_cart_deposit')==0) {
			$invoice->getOrder()->setTotalPaid(
				$invoice->getOrder()->getTotalPaid()+$invoice->getGrandTotal()
			);
			$invoice->getOrder()->setBaseTotalPaid(
				$invoice->getOrder()->getBaseTotalPaid()+$invoice->getBaseGrandTotal()
			);
		} else {
			$invoice->getOrder()->setTotalPaid(
				( $invoice->getOrder()->getTotalPaid()+$invoice->getGrandTotal() )/ Mage::getStoreConfig('octopus/deposit/shopping_cart_deposit')
			);
			$invoice->getOrder()->setBaseTotalPaid(
				( $invoice->getOrder()->getBaseTotalPaid()+$invoice->getBaseGrandTotal() )/ Mage::getStoreConfig('octopus/deposit/shopping_cart_deposit')
			);
		}
			
	}
}
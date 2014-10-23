<?php require_once '../../app/Mage.php';

/*
 * Initialize Magento. Older versions may require Mage::app() instead.
 */
Mage::app('admin');

$sku = Mage::app()->getRequest()->getParam('sku') ? Mage::app()->getRequest()->getParam('sku') : "jsr";
$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);

/*
 * Get all unique order IDs for items with a particular SKU.
 */
$orderItems = Mage::getResourceModel('sales/order_item_collection')
    ->addFieldToFilter('sku', array('like'=>array($sku."%")))
    ->toArray(array('order_id'));

$orderIds = array_unique(array_map(
    function($orderItem) {
        return $orderItem['order_id'];
    },
    $orderItems['items']
));

/*
 * Now get all unique customers from the orders of these items.
 */
$orderCollection = Mage::getResourceModel('sales/order_collection')
    ->addFieldToFilter('entity_id',   array('in'  => $orderIds))
  //  ->addFieldToFilter('customer_id', array('neq' => 'NULL'));
//$orderCollection->getSelect()->group('customer_id');
?>
<html>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.0/css/jquery.dataTables.css">
  
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>



	
	
<title>Customers who bought <?php echo $sku; ?> </title>
<style>
table {
	border-collapse: collapse;
}
td {
	padding: 5px;
	border: 1px solid #000000;
}
.canceled{color:red;}
</style>
</head>
<body>
<div style="float: right;position: relative;width: 180px;"><img src="assets/img/jsr-vip-ticketing.jpg" alt="" style="max-width: 100%;"></div>
<h2><?php echo "<br>".$product->getName()." <br>SKU: ".$sku ?></h2>
<table class="responsive dynamicTable display table table-bordered">
<thead>
	<tr>
	<th>Row</th>
	<th>Name</th>
	<th>Email</th>
        <th>Date</th>
        <th>Discount (If any)</th>
	<th>Skus on order</th>
	<th>Order Total (pre shipping)</th>

</tr>
</thead>
<?php $a = 1; // define the row ?>
<?php
//load the customers info for the order
	foreach ($orderCollection as $order) {
			//$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

			// Get all the order Items and implode them; then use $order->getProductSkus() to call them
			$skus = array();
			foreach ($order->getAllVisibleItems() as $item) {
                 $skus[] = $item->getSku()." (".(int)$item->getData('qty_ordered').")".$vip;
                //$price = $item->getPrice();
			}
			$order->setProductSkus(implode(",", $skus));


			//get order total
			$orderTotal = $order->getGrandTotal() - $order->getShippingAmount();
					
					if ($order->getStatus() == 'canceled'){
						echo "<tr class='canceled'>"; 
						$canceled = "X";
					}else{
					echo "<tr class='".$order->getStatus()."'>";
					$canceled = " ";
					};
						echo "<td>".$a++." ".$canceled."</td>";
						echo "<td>".$order->getCustomerName()."</td>";
						echo "<td>".$order->getCustomerEmail()."</td>";
						
						echo "<td>".$order->getCreatedAt()."</td>";
						echo "<td>".$order->getDiscountAmount()."<br>". $order->getCouponCode()."</td>";
						echo "<td>".$order->getProductSkus()."</td>";
						echo "<td>$ ".$orderTotal."</td>";
					echo "</tr>";
				



			//Zend_Debug::dump($order->debug());
	}

?>

</table>


<br><h2>Email List</h2><br>

<?php
foreach ($orderCollection as $order) {
$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
		
			echo $customer->getEmail().", ";
			}

?>
</body>


<script type="text/javascript">
$(document).ready( function () {
    $('.dynamicTable').DataTable(
	 {
  "pageLength": 50
	} 
);
} );

/* 	if($('table').hasClass('dynamicTable')){
		$('.dynamicTable').dataTable({
			"sPaginationType": "full_numbers",
			"bJQueryUI": false,
			"bAutoWidth": false,
			"bLengthChange": true,
			"sDom": 'T<"clear">lfrtip',
		"oTableTools": {
			"sSwfPath": "plugins/tabletools/swf/copy_csv_xls_pdf.swf"
		}
		});
	}; */
</script>
</html>
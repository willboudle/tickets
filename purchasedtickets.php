<?php

require_once('../../app/Mage.php'); //Path to Magento
umask(0);
Mage::app('admin');

$sku = Mage::app()->getRequest()->getParam('sku') ? Mage::app()->getRequest()->getParam('sku') : "jsr";



$results = Mage::getModel('catalog/product')->getCollection()
->addAttributeToSelect('*')
//	->addAttributeToSelect('sku')
//	->addAttributeToSelect('name')
    ->addAttributeToFilter(
        'status',
        array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    )
	->addFieldToFilter('sku', array('like'=>array($sku."%")))
	->addFieldToFilter('product_type', array('eq'=>'31'))
	;
	 
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<title>Shows</title>
</head>

<body>
<!-- <pre>
<?php // var_dump($results);?>
</pre> -->
    <div class="container">
    <header><img src="assets/img/jsr-vip-ticketing.jpg" alt=""/> </header>
    <p>Choose your show!</p>
    <form name="input" action="purchases.php" method="post">
    <select name="sku" class="form-control">
    <?php foreach($results as $result) :?>
        <?php //foreach($result as $res) :?>

            <option value="<?php echo $result->getData('sku'); ?>"><?php echo $result->getData('name'); ?></option>
        <?php //endforeach;?>
    <?php endforeach;?>

    </select><br>
    <input type="submit" value="Submit" class="btn btn-default btn-lg">
    </form>
</div>
</body>
</html>


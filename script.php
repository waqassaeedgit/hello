<?php
    // Starting clock time in seconds
    $start_time = microtime(true);     
    use Magento\Framework\App\Bootstrap;
    use Magento\Framework\Setup\ModuleContextInterface;
    require __DIR__ . "/app/bootstrap.php";
    $params = $_SERVER;
    $bootstrap = Bootstrap::create(BP, $params);
    $obj = $bootstrap->getObjectManager();
    $state = $obj->get("Magento\Framework\App\State");
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $setup = $objectManager->create("Magento\Framework\Setup\ModuleDataSetupInterface");
    $resource = $objectManager->get("Magento\Framework\App\ResourceConnection");
    $connection = $resource->getConnection();
    //adding table 
    $installer = $setup;
    $installer->startSetup();
    $table = $installer
        ->getConnection()
        ->newTable($installer->getTable("product_prices")) 
        ->addColumn(                                          
            "id",
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [
                "identity" => true,
                "unsigned" => true,
                "nullable" => false,
                "primary" => true,
            ],
            "Autoincremental ID"
        )
        ->addColumn(                                       
            "price",
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            255,
            ["nullable" => true],
            "Price"
        )

        ->addColumn(
            "sku",
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            "255",
            ["nullable" => false],
            "SKU"
        );
    $installer->getConnection()->createTable($table);
    $installer->endSetup();
    $result1 = $connection->fetchAll("SELECT * FROM product_prices");
    $productCollection = $obj->create("Magento\Catalog\Model\ResourceModel\Product\CollectionFactory");
    $collections = $productCollection->create()->addAttributeToSelect("*")->load();
    $test = $productCollection->create();
    foreach ($collections as $product) {
        $x = 0;
        $test = $product->getData();
        foreach ($result1 as $value) {
            $test1 = $value;   
            if ($test["sku"] == $test1["sku"]) {
                $x++;
            }
        }
        //insert data into database
        if ($x == 0) {
            $query = "INSERT INTO `product_prices`(`price`, `sku`) VALUES ('$test[price]', '$test[sku]')";
            $connection->query($query);
        }
    }
    $end_time = microtime(true);
    // Calculate script execution time
    $execution_time = $end_time - $start_time;
    echo "Script run Successfully and Execution time of script = ".$execution_time ." Sec\n";
?>

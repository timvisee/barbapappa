<?php

namespace app\product;

use app\config\Config;
use app\database\Database;
use app\money\MoneyAmount;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class ProductManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'product';

    /**
     * Get the database table name of the products.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all products.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All products.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getProducts() {
        // Build a query to select the products
        $query = 'SELECT product_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of products
        $products = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $products[] = new Product($data['product_id']);

        // Return the list of products
        return $products;
    }

    /**
     * Get the number of products.
     *
     * @return int Number of products.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getProductCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT product_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any product with the specified ID.
     *
     * @param int $id The ID of the product to check for.
     *
     * @return bool True if any product exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isProductWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid product ID.');

        // Prepare a query for the database to list products with this ID
        $statement = Database::getPDO()->prepare('SELECT product_id FROM ' . static::getDatabaseTableName() . ' WHERE product_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any product found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Create a new product.
     *
     * @param ProductCategory|null $productCategory The product category, or null if none.
     * @param string $name The product name.
     * @param MoneyAmount|int $price The price as money amount instance, or as integer in cents.
     *
     * @return Product The created product as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createProduct($productCategory, $name, $price) {
        // Determine the product category ID
        $productCategoryId = null;

        // Make sure the product category is valid
        if($productCategory !== null) {
            // Get the product category ID from a product category instance
            if($productCategory instanceof ProductCategory)
                $productCategoryId = $productCategory->getId();

            // The product category seems to be invalid
            throw new Exception('Invalid product category.');
        }

        // Trim the product name
        $name = trim($name);

        // Parse the product price
        $price = MoneyAmount::parseMoneyAmountValue($price);

        // Determine the creation date time
        $createDateTime = DateTime::now();

        // Prepare a query for the product being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (product_category_id, product_name, product_price, product_creation_datetime) ' .
            'VALUES (:category_id, :product_name, :price, :creation_datetime)');
        $statement->bindValue(':category_id', $productCategoryId, PDO::PARAM_INT);
        $statement->bindValue(':product_name', $name, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':creation_datetime', $createDateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the product instance
        return new Product(Database::getPDO()->lastInsertId());
    }
}

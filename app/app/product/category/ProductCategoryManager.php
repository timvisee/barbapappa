<?php

namespace app\product\category;

use app\config\Config;
use app\database\Database;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class ProductCategoryManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'product_category';

    /**
     * Get the database table name of the product categories.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all product categories.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All product categories.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getProductCategories() {
        // Build a query to select the product categories
        $query = 'SELECT product_category_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of product categories
        $productCategories = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $productCategories[] = new ProductCategory($data['product_category_id']);

        // Return the list of product categories
        return $productCategories;
    }

    /**
     * Get the number of product categories.
     *
     * @return int Number of product categories.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getProductCategoryCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT product_category_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any product category with the specified ID.
     *
     * @param int $id The ID of the product category to check for.
     *
     * @return bool True if any product category exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isProductCategoryWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid product category ID.');

        // Prepare a query for the database to list product categories with this ID
        $statement = Database::getPDO()->prepare('SELECT product_category_id FROM ' . static::getDatabaseTableName() . ' WHERE product_category_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any product category found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Create a new product category.
     *
     * @param ProductCategory|int|null $parentCategory Parent product category, parent product category ID, null for no parent category.
     * @param string $name Product category name.
     *
     * @return ProductCategory The created product category as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createProductCategory($parentCategory, $name) {
        // Determine the parent product category ID
        $parentCategoryId = null;

        // Make sure the parent product category is valid
        if($parentCategory !== null) {
            // Make sure the parent product category instance is valid
            if(!($parentCategory instanceof ProductCategory))
                throw new Exception('Invalid product category.');

            // Get the product category ID
            $parentCategoryId = $parentCategory->getId();
        }

        // Trim the product category name
        $name = trim($name);

        // Determine the creation date time
        $createDateTime = DateTime::now();

        // Prepare a query for the product category
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (product_category_parent_id, product_category_name, product_category_creation_datetime) ' .
            'VALUES (:parent_id, :category_name, :creation_datetime)');
        $statement->bindValue(':category_id', $parentCategoryId, PDO::PARAM_INT);
        $statement->bindValue(':category_name', $name, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':creation_datetime', $createDateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the product category instance
        return new ProductCategory(Database::getPDO()->lastInsertId());
    }
}

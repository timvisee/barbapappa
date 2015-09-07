<?php

namespace app\product;

use app\database\Database;
use app\database\DatabaseValueTranslations;
use app\product\category\ProductCategoryManager;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class ProductCategory {

    /** @var int The product category ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id ProductCategory ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the product category ID.
     *
     * @return int The product category ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific product.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list products with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . ProductCategoryManager::getDatabaseTableName() . ' WHERE product_category_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the parent product category category if set.
     *
     * @return ProductCategory|null The parent product category, or null if not set.
     */
    public function getParentCategory() {
        // Get the category ID, return null if it's null
        if(($categoryId = $this->getParentCategoryId()) === null)
            return null;

        // Return the parent product category object
        return new ProductCategory($categoryId);
    }

    /**
     * Get the parent product category ID if set.
     *
     * @return int|null Parent product category ID, or null if not set.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getParentCategoryId() {
        // Get the parent category ID and return null if it's null
        if(($parentId = $this->getDatabaseValue('product_category_parent_id')) === null)
            return null;

        // Cast and return the ID to an int
        return (int) $parentId;
    }

    /**
     * Check if this product has a parent category.
     *
     * @return bool True if this product has a parent category, false if not.
     */
    public function hasParentCategory() {
        return $this->getParentCategory() !== null;
    }

    /**
     * Set the parent category.
     *
     * @param ProductCategory|int|null $parentCategory The parent product category, the parent category ID or null to clear the parent category.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setParentCategory($parentCategory = null) {
        // Parse the parent product category
        if(is_int($parentCategory) || is_numeric($parentCategory))
            $parentCategory = new ProductCategory($parentCategory);

        // Make sure the instance is valid
        if($parentCategory !== null && !($parentCategory instanceof ProductCategory))
            throw new Exception('Invalid product category category.');

        // Get the parent category ID
        $productCategoryId = null;
        if($parentCategory instanceof ProductCategory)
            $productCategoryId = $parentCategory->getId();

        // Prepare a query to set the product category category
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductCategoryManager::getDatabaseTableName() .
            ' SET product_category_parent_id=:parent_id' .
            ' WHERE product_category_id=:category_id');
        $statement->bindValue(':category_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':parent_id', $productCategoryId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Get the product category name.
     *
     * @return string Product category name.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getName() {
        return $this->getDatabaseValue('product_category_name');
    }

    /**
     * Set the product category name.
     *
     * @param string $name The product category name.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setName($name) {
        // Prepare a query to set the product category category
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductCategoryManager::getDatabaseTableName() .
            ' SET product_category_name=:name' .
            ' WHERE product_category_id=:category_id');
        $statement->bindValue(':category_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Get the product category translations, with the product name as default.
     *
     * @return DatabaseValueTranslations The translations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function getTranslations() {
        // Get the translation values from the database
        $values = $this->getDatabaseValue('product_category_name_translations');

        // Get the default value
        $default = $this->getName();

        // Construct and return the database value translation object with the name as default
        return new DatabaseValueTranslations($values, $default);
    }

    /**
     * Set the product category name translations.
     *
     * @param DatabaseValueTranslations|null $translations The product category name translations as database value
     * translations instance, or null to clear the product name translations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setTranslations($translations) {
        // Make sure the translations object is valid
        if($translations !== null && !($translations instanceof DatabaseValueTranslations))
            throw new Exception('Invalid database value translations instance.');

        // Cast the database value translations instance to a string by encoding the values to a JSON array
        $translations = $translations->getValuesEncoded();

        // Prepare a query to set the product translations
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductManager::getDatabaseTableName() .
            ' SET product_category_name_translations=:name_translations' .
            ' WHERE product_category_id=:product_id');
        $statement->bindValue(':product_category__id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name_translations', $translations, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Get the product's creation date time.
     *
     * @return DateTime ProductCategory's creation date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('product_category_creation_datetime'));
    }
}

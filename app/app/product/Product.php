<?php

namespace app\product;

use app\database\Database;
use app\database\DatabaseValueTranslations;
use app\money\MoneyAmount;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Product {

    /** @var int The product ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Product ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the product ID.
     *
     * @return int The product ID.
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
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . ProductManager::getDatabaseTableName() . ' WHERE product_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the product category if set.
     *
     * @return ProductCategory|null The product category, or null if not set.
     */
    public function getProductCategory() {
        // Get the category ID, return null if it's null
        if(($categoryId = $this->getProductCategoryId()) === null)
            return null;

        // Return the product category object
        return new ProductCategory($categoryId);
    }

    /**
     * Get the product category ID if set.
     *
     * @return int|null Product category ID, or null if not set.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getProductCategoryId() {
        // Get the category ID and return null if it's null
        if(($categoryId = $this->getDatabaseValue('product_category_id')) === null)
            return null;

        // Cast and return the ID to an int
        return (int) $categoryId;
    }

    /**
     * Check if this product has a product category.
     *
     * @return bool True if this product has a category, false if not.
     */
    public function hasProductCategory() {
        return $this->getProductCategory() !== null;
    }

    /**
     * Set the product category.
     *
     * @param ProductCategory|int|null $productCategory [optional] Product category, product category ID or null to clear the product category.
     * @param bool $updateModificationDateTime [optional] True to update the modification date time, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setProductCategory($productCategory = null, $updateModificationDateTime = true) {
        // Parse the product category
        if(is_int($productCategory) || is_numeric($productCategory))
            $productCategory = new ProductCategory($productCategory);

        // Make sure the instance is valid
        if($productCategory !== null && !($productCategory instanceof ProductCategory))
            throw new Exception('Invalid product category.');

        // Get the product category ID
        $productCategoryId = null;
        if($productCategory instanceof ProductCategory)
            $productCategoryId = $productCategory->getId();

        // Prepare a query to set the product category
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductManager::getDatabaseTableName() .
            ' SET product_category_id=:category_id' .
            ' WHERE product_id=:product_id');
        $statement->bindValue(':product_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':category_id', $productCategoryId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the modification date time
        if($updateModificationDateTime)
            $this->setModifiedDateTime();
    }

    /**
     * Get the product name.
     *
     * @return string Product name.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getName() {
        return $this->getDatabaseValue('product_name');
    }

    /**
     * Set the product name.
     *
     * @param string $name The product name.
     * @param bool $updateModificationDateTime [optional] True to update the modification date time, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setName($name, $updateModificationDateTime = true) {
        // Prepare a query to set the product category
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductManager::getDatabaseTableName() .
            ' SET product_name=:name' .
            ' WHERE product_id=:product_id');
        $statement->bindValue(':product_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the modification date time
        if($updateModificationDateTime)
            $this->setModifiedDateTime();
    }

    /**
     * Get the product translations, with the product name as default.
     *
     * @return DatabaseValueTranslations The translations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function getTranslations() {
        // Get the translation values from the database
        $values = $this->getDatabaseValue('product_name_translations');

        // Get the default value
        $default = $this->getName();

        // Construct and return the database value translation object with the name as default
        return new DatabaseValueTranslations($values, $default);
    }

    /**
     * Set the product name translations.
     *
     * @param DatabaseValueTranslations|null $translations The product name translations as database value translations
     * instance, or null to clear the product name translations.
     * @param bool $updateModificationDateTime [optional] True to update the product modification date time, false if not.
     *
     * @throws Exception
     */
    public function setTranslations($translations, $updateModificationDateTime = true) {
        // Make sure the translations object is valid
        if($translations !== null && !($translations instanceof DatabaseValueTranslations))
            throw new Exception('Invalid database value translations instance.');

        // Cast the database value translations instance to a string by encoding the values to a JSON array
        $translations = $translations->getValuesEncoded();

        // Prepare a query to set the product translations
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductManager::getDatabaseTableName() .
            ' SET product_name_translations=:name_translations' .
            ' WHERE product_id=:product_id');
        $statement->bindValue(':product_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name_translations', $translations, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the modification date time
        if($updateModificationDateTime)
            $this->setModifiedDateTime();
    }

    /**
     * Get the product price.
     *
     * @return MoneyAmount Product price.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getPrice() {
        // Get the price value and make sure it's valid
        if(!is_numeric($price = $this->getDatabaseValue('product_price')))
            return null;

        // Return the price as money amount instance
        return new MoneyAmount($price);
    }

    /**
     * Set the product price.
     *
     * @param MoneyAmount|int $price The price as money amount instance, or the price in cents.
     * @param bool $updateModificationDateTime [optional] True to update the modification date time, false if not.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setPrice($price, $updateModificationDateTime = true) {
        // Parse the price
        $price = MoneyAmount::parseMoneyAmountValue($price);

        // Prepare a query to set the product price
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductManager::getDatabaseTableName() .
            ' SET product_price=:price' .
            ' WHERE product_id=:product_id');
        $statement->bindValue(':product_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':price', $price, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the modification date time
        if($updateModificationDateTime)
            $this->setModifiedDateTime();
    }

    /**
     * Get the product's creation date time.
     *
     * @return DateTime Product's creation date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('product_creation_datetime'));
    }

    /**
     * Get the product's modified date time.
     *
     * @return DateTime Product's modified date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getModifiedDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('product_modified_datetime'));
    }

    /**
     * Set the product's modification date time.
     *
     * @param DateTime|null $dateTime [optional] The modification date time, or null to use the current.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setModifiedDateTime($dateTime = null) {
        // Parse the date time and make sure it's valid
        if(($dateTime = DateTime::parse($dateTime)) === null)
            throw new Exception('Invalid date time.');

        // Prepare a query to set the product price
        $statement = Database::getPDO()->prepare('UPDATE ' . ProductManager::getDatabaseTableName() .
            ' SET product_modified_datetime=:modified_datetime' .
            ' WHERE product_id=:product_id');
        $statement->bindValue(':product_id', $this->getId(), PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':modified_datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}

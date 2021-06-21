<?php

namespace Snaptec\Productlist\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installSql($setup, $context);
    }

    public function installSql(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $context;
        $installer = $setup;
        $installer->startSetup();

        /**
         * Creating table snaptec_product_list
         */
        $table_key_name = $installer->getTable('snaptec_product_list');
        $this->checkTableExist($installer, $table_key_name, 'snaptec_product_list');
        $table_key = $installer->getConnection()->newTable(
            $table_key_name
        )->addColumn(
            'productlist_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product List Id'
        )->addColumn(
            'list_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Title'
        )->addColumn(
            'list_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'List Image'
        )->addColumn(
            'list_image_tablet',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'List Image Tab'
        )->addColumn(
            'list_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Type'
        )->addColumn(
            'list_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'List Products'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '2M',
            ['nullable' => true, 'default' => 0],
            'Category Id'
        )->addColumn(
            'list_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'status'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Sort Order'
        );
        $installer->getConnection()->createTable($table_key);
        // end create table snaptec_product_list

        $installer->endSetup();
    }

    public function checkTableExist($installer, $table_key_name, $table_name)
    {
        if ($installer->getConnection()->isTableExists($table_key_name) == true) {
            $installer->getConnection()
                    ->dropTable($installer->getConnection()->getTableName($table_name));
        }
    }
}

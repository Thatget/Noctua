<?php
/**
 * @author      Vladimir Popov
 * @copyright   Copyright © 2019 Vladimir Popov. All rights reserved.
 */

namespace VladimirPopov\WebForms\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.7.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'email_customer_sender_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Sender name for customer email'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.7.4', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('webforms_results'),
                'approved',
                'approved',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => false,
                    'nullable' => false,
                    'comment' => 'Approved status'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.7.6', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'bcc_admin_email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'BCC Admin Email'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'bcc_customer_email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'BCC Customer Email'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'bcc_approval_email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'BCC Approval Email'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.7.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'accept_url_parameters',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Accept URL parameters'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.7.8', '<')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('webforms_files'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ], 'Id')
                ->addColumn('result_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'nullable' => false,
                    'unsigned' => true,
                ], 'Result ID')
                ->addColumn('field_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'nullable' => false,
                    'unsigned' => true,
                ], 'Field ID')
                ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                    'nullable' => false
                ], 'File Name')
                ->addColumn('size', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true
                ], 'File Size')
                ->addColumn('mime_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false
                ], 'Mime Type')
                ->addColumn('path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                    'nullable' => false
                ], 'File Path')
                ->addColumn('link_hash', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false
                ], 'Link Hash')
                ->addColumn('created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [
                        'nullable' => false
                    ]
                );

            $table->addForeignKey(
                $setup->getFkName('webforms_files', 'result_id', 'webforms_results', 'id'),
                'result_id',
                $setup->getTable('webforms_results'),
                'id');

            $table->addForeignKey(
                $setup->getFkName('webforms_files', 'field', 'webforms_fields', 'id'),
                'field_id',
                $setup->getTable('webforms_fields'),
                'id');

            $setup->getConnection()->createTable($table);
        }


        if (version_compare($context->getVersion(), '2.8.4', '<')) {

            $table = $setup->getConnection()
                ->newTable($setup->getTable('webforms_dropzone'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ], 'Id')
                ->addColumn('field_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'nullable' => false,
                    'unsigned' => true,
                ], 'Field ID')
                ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                    'nullable' => false
                ], 'File Name')
                ->addColumn('size', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'nullable' => true,
                    'unsigned' => true
                ], 'File Size')
                ->addColumn('mime_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false
                ], 'Mime Type')
                ->addColumn('path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                    'nullable' => false
                ], 'File Path')
                ->addColumn('hash', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false
                ], 'Hash')
                ->addColumn('created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [
                        'nullable' => false
                    ]
                );

            $setup->getConnection()->createTable($table);

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms_fields'),
                    'validate_unique',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 1,
                        'comment' => 'Validate Unique'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms_fields'),
                    'validate_unique_message',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'comment' => 'Validate Unique Message'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms_fields'),
                    'browser_autocomplete',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'comment' => 'Browser Autocomplete'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms'),
                    'frontend_download',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 1,
                        'comment' => 'Frontend Download'
                    ]
                );
        }

        if (version_compare($context->getVersion(), '2.8.6', '<')) {

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms'),
                    'customer_result_permissions_serialized',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'comment' => 'Customer Result Permissions'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms'),
                    'delete_submissions',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 1,
                        'comment' => 'Delete Submissions'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms'),
                    'purge_enable',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 1,
                        'comment' => 'Purge Enable'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms'),
                    'purge_period',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => 10,
                        'comment' => 'Purge Period'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms_fields'),
                    'hide_label',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 1,
                        'comment' => 'Hide Label'
                    ]
                );
        }

        if (version_compare($context->getVersion(), '2.8.7', '<')) {

            // GDPR settings
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webforms'),
                    'show_gdpr_agreement_text',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 1,
                        'comment' => 'Show GDPR Text'
                    ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'gdpr_agreement_text',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'GDPR Text'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'show_gdpr_agreement_checkbox',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Show GDPR Checkbox'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'gdpr_agreement_checkbox_required',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'GDPR Checkbox Required'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'gdpr_agreement_checkbox_do_not_store',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'GDPR Checkbox Do Not Store'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'gdpr_agreement_checkbox_label',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'GDPR Checkbox Label'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'gdpr_agreement_checkbox_error_text',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'GDPR Error Text'
                ]);
        }

        if (version_compare($context->getVersion(), '2.8.8', '<')) {

            // Field inline elements option
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'inline_elements',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Inline elements'
                ]);

            // Field responsive width
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'width_lg',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Large Screen Width'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'width_md',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Medium Screen Width'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'width_sm',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Small Screen Width'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'row_lg',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Large screen start new row'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'row_md',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Medium screen start new row'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'row_sm',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Small screen start new row'
                ]);

            // Fieldset responsive width
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'width_lg',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Large Screen Width'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'width_md',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Medium Screen Width'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'width_sm',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Small Screen Width'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'row_lg',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Large screen start new row'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'row_md',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Medium screen start new row'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'row_sm',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Small screen start new row'
                ]);

            // Fieldset CSS style
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fieldsets'),
                'css_style',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'CSS Style'
                ]);


            // Form responsive width controls
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'show_width_lg',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Large Screen Width Controls'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'show_width_md',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Medium Screen Width Controls'
                ]);

            $setup->getConnection()->addColumn(
                $setup->getTable('webforms'),
                'show_width_sm',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Small Screen Width Controls'
                ]);
        }

        if (version_compare($context->getVersion(), '2.8.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webforms_fields'),
                'custom_attributes',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Custom attributes'
                ]);
        }

        $setup->endSetup();
    }
}
<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qsoft\ContentCustom\Api\Data;

/**
 * CMS block interface.
 * @api
 * @since 100.0.2
 */
interface LinkReplaceInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LINK_ID      = 'link_id';
    const LINK_SEARCH  = 'link_search';
    const LINK_REPLACE = 'link_replace';
    const FULL_ACTION_NAME   = 'full_action_name';
    const OBJECT_ID     = 'object_id';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getLinkSearch();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getLinkReplace();
    /**
     * Get content
     *
     * @return string|null
     */
    public function getFullActionName();
    /**
     * Get content
     *
     * @return string|null
     */
    public function getObjectId();

    /**
     * Set ID
     *
     * @param int $id
     * @return BlockInterface
     */
    public function setId($id);

    /**
     * Set title
     *
     * @param string $title
     * @return BlockInterface
     */
    public function setLinkSearch($title);

    /**
     * Set content
     *
     * @param string $content
     * @return BlockInterface
     */
    public function setLinkReplace($content);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return BlockInterface
     */
    public function setFullActionName($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return BlockInterface
     */
    public function setObjectId($updateTime);
}

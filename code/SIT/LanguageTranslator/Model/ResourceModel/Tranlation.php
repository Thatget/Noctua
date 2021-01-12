<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tranlation extends AbstractDb
{
	protected function _construct()
	{
		$this->_init('sit_translation', 'translation_id');
	}

	protected function saveStore($objects)
	{
		// $conditions = $this->getConnection()->quoteInto('translation_id = ?', $objects->getTranslationId());
		// $this->getConnection()->delete($this->getTable('sit_storewise_translation'), $conditions);
		foreach ((array) $objects->getData('frontend_label') as $key => $stores) {
			if ($stores == null) {
				$stores = $objects->getOriginalString();
			}
			$storeArrays = [
				'translation_id' => $objects->getId(),
				'translated_string' => $stores,
				'store_id' => $key,
			];
			$tUpdate = $this->getConnection()->update(
				$this->getTable('sit_storewise_translation'),
				$storeArrays,
				['translation_id = ?' => $objects->getTranslationId(), 'store_id = ?' => $key]
			);
			if ($tUpdate == 0)
				$this->getConnection()->insert(
					$this->getTable('sit_storewise_translation'),
					$storeArrays
				);
		}
	}

	protected function _afterSave(\Magento\Framework\Model\AbstractModel $objects)
	{
		if (!$objects->getIsMassStatus()) {
			$this->saveStore($objects);
		}

		return parent::_afterSave($objects);
	}

	public function loadStore($id)
	{
		$data = [];
		$select = $this->getConnection()->select()
			->from($this->getTable('sit_storewise_translation'))
			->where('translation_id = ?', $id);

		if ($data = $this->getConnection()->fetchAll($select)) {
			return $data;
		}
	}
}

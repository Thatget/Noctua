<?php

namespace SIT\WebformApi\Model;

use SIT\WebformApi\Api\DeliveryInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class DeliveryPrice implements DeliveryInterface {

	protected $date;
	protected $timezone;
	protected $_directoryList;
	protected $countryprice;
	protected $webformResultFactory;

	public function __construct(
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\SIT\WebformApi\Model\Countryprice $countryprice,
		\VladimirPopov\WebForms\Model\ResultFactory $webformResultFactory
	) {
		$this->timezone = $timezone;
		$this->_directoryList = $directoryList;
		$this->countryprice = $countryprice;
		$this->webformResultFactory = $webformResultFactory;
		$this->date = $date;
	}
	/**
	 * Return the sum of the two numbers.
	 *
	 * @api
	 * @param mixed $deliveryprice
	 * @return array
	 */
	public function expressdeliveryapi($deliveryprice) {
		$arr_webforms = [];
		$arr_up_data = [];

		//$deliveryprice = json_decode($deliveryprice);

		$delivery_id_arr = [];
		$var = 2;

		foreach ($deliveryprice as $key => $value1) {
			if ($value1[2] == "") {
				$value1[2] = "data:image/png;base64,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";
			}
			$file_name = $this->base64ToImage($value1[2]);
			$arr_data = array("country_id" => $key, "price" => $value1[0], "delivery_type" => $value1[1], "ship_icon" => $file_name, "delivery_id" => $value1[3], "delivery_duration" => $value1[4]);
			$arr_webforms[$key] = $value1;
			$base64string = $arr_webforms[$key][2];
			preg_match("/^data:image\/(.*);base64/i", $base64string, $match);
			$extension = $match[1];
			$key_string = $arr_webforms[$key];
			$get_id = $this->countryprice->getCollection()->addFieldToFilter("delivery_id", $value1[3])->getData();
			$country_col = $this->countryprice->getCollection()->addFieldToFilter('country_id', $key);
			foreach ($country_col as $key => $value) {
				$delivery_id_arr[] = $value->getDeliveryId();
			}
			if (count($country_col) > 0) {
				if (!(in_array($value1[3], $delivery_id_arr))) {
					$var = 0;
				} else {
					$var = 1;
				}
			} else {
				$var = 0;
			}
			if (($var == "1") || ($var == "0")) {
				if (($extension == "jpg") || ($extension == "jpeg") || ($extension == "png") || ($extension == "gif")) {
					if (count($get_id) == 0) {
						$model = $this->countryprice->setData($arr_data);
						$model->save();
					} else {
						foreach ($get_id as $key => $value) {
							$model = $this->countryprice->load($value["id"])->addData($arr_data);
							$model->setId($value["id"])->save();
						}
					}
				}
			}
		}

		if (($extension == "jpg") || ($extension == "jpeg") || ($extension == "png") || ($extension == "gif")) {
			if (($var == "0")) {
				$msg = array($var, "'Record added Successfully'");
			}
			if ($var == "1") {
				$msg = array($var, "'Record updated Successfully'");
			}
		} else {
			$var = 2;
			$msg = array($var, "'Error: " . $extension . " file format is not supported'");
		}
		return $msg;
	}

	public function base64ToImage($data) {
		/*Start code for create image from base64 image data*/
		/*Start -- code done by Angel*/
		$splited = explode("base64,", $data);
		$IMGdata = $splited[1];
		$ext = explode("data:image/", $splited[0]);
		$extension = str_replace(";", "", $ext[1]);
		$return_file_name = "images/shipicon/icon_" . rand() . '.' . $extension;
		$file = $this->_directoryList->getPath('media') . "/" . $return_file_name;
		file_put_contents($file, base64_decode($IMGdata));
		//the image to be rotated
		$image = $file;
		//rotation angle
		$degrees = 90;
		//load the image
		$source = imagecreatefrompng($image);
		//rotate the image
		$rotate = imagerotate($source, $degrees, 0);
		imagepng($rotate, $file);
		//free the memory
		imagedestroy($source);
		imagedestroy($rotate);
		/*End code for create image from base64 image data*/
		/*End -- code done by Angel*/
		return $return_file_name;
	}
}

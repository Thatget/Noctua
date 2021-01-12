<?php
namespace MW\FixSpecialCharOnEmailSubject\Plugin\Email\Model;

/**
 * Class Template
 * @package MW\FixSpecialCharOnEmailSubject\Plugin\Email\Model
 */
class Template
{
    /**
     * Decode the special character in subject string
     *
     * @param \Magento\Email\Model\Template $subject
     * @param $result
     * @return string
     */
    public function afterGetSubject(
        \Magento\Email\Model\Template $subject,
        $result
    ) {
        return htmlspecialchars_decode((string)$result, ENT_QUOTES);
    }
}
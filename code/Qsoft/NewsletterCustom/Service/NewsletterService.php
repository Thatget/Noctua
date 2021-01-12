<?php

namespace Qsoft\NewsletterCustom\Service;

/**
 * Class NewsletterService
 *
 * @package Qsoft\NewsletterCustom\Service
 */
class NewsletterService
{
    /**
     * Process Newsletter Text
     *
     * @param string $newsletterText
     * @return string|string[]
     */
    public function processNewsletterText($newsletterText)
    {
        return $newsletterText;

        // not need to replace
        $urlArr = [];
        $startUrlArr = [];
        $i = 0;
        if (strpos($newsletterText, "<a href")) {
            return $newsletterText;
        }
        // Get total http position from template
        while (1) {
            $last = strripos($newsletterText, "http");
            $first = strpos($newsletterText, "http", $i);
            if ($first && $first > 0) {
                $startUrlArr[] = $first;
                $i = $first + 1;
            }
            if ($first == $last) {
                break;
            }
        }

        // get Url
        if (count($startUrlArr)) {
            foreach ($startUrlArr as $start) {
                $first = strpos($newsletterText, " ", $start);

                if ($first && $first > 0) {
                    $end = $first + 1;
                } else {
                    $end = strlen($newsletterText) - 1;
                }

                $size = $end - $start;
                $url = substr($newsletterText, $start, $size);
                $realUrl = "";
                $end = strlen($url) - 1;
                for ($i = 0; $i < strlen($url); $i++) {
                    if (strcmp($url[$i], " ") || strcmp($url[$i], "\n")) {
                        $end = $i;
                        break;
                    } else {
                        $realUrl .= $url[$i];
                    }
                }
                $urlArr[] = $url;
            }
        }
        $urlArr = array_unique($urlArr);

        // Replace Url
        if (count($urlArr)) {
            foreach ($urlArr as $url) {
                $realUrl = substr($url, 0, strlen($url) - 1);

                $newsletterText = str_replace($url, " <a href=" . $realUrl . ">" . $realUrl . "</a> ", $newsletterText);
            }
        }

        return $newsletterText;
    }
}

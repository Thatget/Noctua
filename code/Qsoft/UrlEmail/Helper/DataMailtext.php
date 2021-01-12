<?php


namespace Qsoft\UrlEmail\Helper;

/**
 * Class Config
 * @package Qsoft\UrlEmail\Helper
 */
class DataMailtext
{
    /**
     * @param $text
     * @return string|string[]|null
     */
    public function linkOfPlainText($text)
    {
        $rexProtocol = '(https?://)?';
        $rexDomain = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
        $rexPort = '(:[0-9]{1,5})?';
        $rexPath = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
        $rexQuery = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';

        if (preg_match('/<\s?[^\>]*\/?\s?>/i', $text) == false) {
            $text = preg_replace_callback(
                "&\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))&",
                function ($match) {
                    $completeUrl = $match[1] ? $match[0] : "http://{$match[0]}";
                    return '<a href="' . $completeUrl . '">'
                        . $match[1] . $match[2] . $match[3] . $match[4] . '</a>';
                },
                $text
            );
            // $text = nl2br($text);
        }
        return $text;
    }

    /**
     * @param $text
     * @return string|string[]|null
     */
	public function linkFunc($text)
    {
        $text = preg_replace_callback(
            "~((?={{var\s)(.*)(?=Link\(|Url\()(.*=*}}))(?!<\/a>|\")~",
            function ($match) {
                return '<a href="' . $match[1] . '">'
                    . $match[1] . '</a>';
            },
            $text);
//        $text = nl2br($text);
        return $text;
    }

    /**
     * @param $text
     * @return string|string[]|null
     */
    public function linkAllEmail($text)
    {
        /* 
        if (preg_match('/<\s?[^\>]*\/?\s?>/i', $text) == false) {
            $text = $this->linkify($text);
            $text = nl2br($text);
        } else {
            $text = $this->linkify($text);
        }
        */
        $text = $this->linkFunc($text);
        return $text;
    }

    /**
     * @param $value
     * @param array $protocols
     * @param array $attributes
     * @param string $mode
     * @return string|string[]|null
     */
    function linkify($value, $protocols = ['http', 'mail'], $attributes = [], $mode = 'normal')
    {
        // Link attributes
        $attr = '';
        foreach ($attributes as $key => $val) {
            $attr = ' ' . $key . '="' . htmlentities($val) . '"';
        }

        $links = [];

        // Extract existing links and tags
        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i',
            function ($match) use (&$links) {
                return '<' . array_push($links, $match[1]) . '>';
            },
            $value);

        // Extract text links for each protocol
        foreach ((array)$protocols as $protocol) {
            switch ($protocol) {
                case 'http':
                case 'https':
                    $value = preg_replace_callback($mode != 'all' ? '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i' : '~([^\s<]+\.[^\s<]+)(?<![\.,:])~i',
                        function ($match) use ($protocol, &$links, $attr) {
                            if ($match[1]) $protocol = $match[1];
                            $link = $match[2] ?: $match[3];
                            return '<' . array_push($links, '<a' . $attr . ' href="' . $protocol . '://' . $link . '">' . $protocol . '://' . $link . '</a>') . '>';
                        },
                        $value);
                    break;
                case 'mail':
                    $value = preg_replace_callback('~([a-zA-Z\d\.]+?@[^\s<]+?(\.[a-z]{2,3})+)(?<![\.,:])~',
                        function ($match) use (&$links, $attr) {
                            return '<' . array_push($links, '<a' . $attr . ' href="mailto:' . $match[1] . '">' . $match[1] . '</a>') . '>';
                        },
                        $value);
                    break;
                case 'twitter':
                    $value = preg_replace_callback('~(?<!\w)[@#](\w++)~',
                        function ($match) use (&$links, $attr) {
                            return '<' . array_push($links, '<a' . $attr . ' href="https://twitter.com/' . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1] . '">' . $match[0] . '</a>') . '>';
                        }, $value);
                    break;
                default:
                    $value = preg_replace_callback($mode != 'all' ? '~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i' : '~([^\s<]+)(?<![\.,:])~i',
                        function ($match) use ($protocol, &$links, $attr) {
                            return '<' . array_push($links, '<a' . $attr . ' href="' . $protocol . '://' . $match[1] . '">' . $match[1] . '</a>') . '>';
                        }, $value);
                    break;
            }
        }

        // Insert all link
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
            return $links[$match[1] - 1];
        }, $value);
    }
}

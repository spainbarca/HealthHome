<?php

namespace phpformbuilder\HtmlFormatter;

/**
 * @link https://github.com/gajus/dindent for the canonical source repository
 * @license https://github.com/gajus/dindent/blob/master/LICENSE BSD 3-Clause
 */

/**
 * Class HtmlFormatter
 *
 * This class represents an HTML formatter used by the PHPFormBuilder library.
 * It provides methods for formatting HTML code.
 */
class HtmlFormatter
{
    /**
     * @var array<mixed> $log An array to store log messages.
     */
    private array $log = array();

    /**
     * @var array<string> $options An array of options for the HTML formatter.
     *                     Default option: 'indentation_character' => '    '
     */
    private array $options = array('indentation_character' => '    ');

    /**
     * @var array<string> $inlineElements An array of inline HTML elements.
     */
    private array $inlineElements = array('b', 'big', 'i', 'small', 'tt', 'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp', 'var', 'a', 'bdo', 'br', 'img', 'span', 'sub', 'sup');

    /**
     * @var array<mixed> $temporaryReplacementsScript An array to store temporary replacements for script tags.
     */
    private array $temporaryReplacementsScript = array();

    /**
     * @var array<mixed> $temporaryReplacementsInline An array to store temporary replacements for inline elements.
     */
    private array $temporaryReplacementsInline = array();

    const ELEMENT_TYPE_BLOCK = 0;
    const ELEMENT_TYPE_INLINE = 1;

    const MATCH_INDENT_NO = 0;
    const MATCH_INDENT_DECREASE = 1;
    const MATCH_INDENT_INCREASE = 2;
    const MATCH_DISCARD = 3;

    /**
     * @param array<string, string> $options
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $name => $value) {
            if (!array_key_exists($name, $this->options)) {
                throw new \InvalidArgumentException('Unrecognized option.');
            }

            $this->options[$name] = $value;
        }
    }

    /**
     * @param string $elementName Element name, e.g. "b".
     * @param int $type
     * @return void
     */
    public function setElementType($elementName, $type)
    {
        if ($type === static::ELEMENT_TYPE_BLOCK) {
            $this->inlineElements = array_diff($this->inlineElements, array($elementName));
        } elseif ($type === static::ELEMENT_TYPE_INLINE) {
            $this->inlineElements[] = $elementName;
        } else {
            throw new \InvalidArgumentException('Unrecognized element type.');
        }

        $this->inlineElements = array_unique($this->inlineElements);
    }

    /**
     * @param string $input HTML input.
     * @return string Indented HTML.
     */
    public function indent(string $input)
    {
        $this->log = array();

        // Dindent does not indent <script> body. Instead, it temporary removes it from the code, indents the input, and restores the script body.
        if (preg_match_all('/<script\b[^>]*>([\s\S]*?)<\/script>/mi', $input, $matches)) {
            $this->temporaryReplacementsScript = $matches[0];
            foreach ($matches[0] as $i => $match) {
                $input = str_replace($match, '<script>' . ($i + 1) . '</script>', $input);
            }
        }

        // Removing double whitespaces to make the source code easier to read.
        // With exception of <pre>/ CSS white-space changing the default behaviour, double whitespace is meaningless in HTML output.
        // This reason alone is sufficient not to use Dindent in production.
        $input = str_replace("\t", '', $input);
        $input = preg_replace('/\s{2,}/u', ' ', $input);

        // Remove inline elements and replace them with text entities. Do this recursively until there is nothing to
        // replace. Store each step in an array.
        $this->temporaryReplacementsInline = array();
        do {
            $inputBeforeReplacement = $input;
            if (preg_match_all('/<(' . implode('|', $this->inlineElements) . ')[^>]*>(?:[^<]*)<\/\1>/', $input ?? '', $matches)) {
                array_unshift($this->temporaryReplacementsInline, $matches[0]);
                foreach ($matches[0] as $i => $match) {
                    $input = str_replace($match, 'ᐃ' . ($i + 1) . 'ᐃ', $input ?? '');
                }
            }
        } while ($inputBeforeReplacement !== $input);


        $subject = $input;

        $output = '';

        $nextLineIndentationLevel = 0;

        do {
            $indentationLevel = $nextLineIndentationLevel;

            $patterns = array(
                // block tag
                '/^(<([a-z]+)(?:[^>]*)>(?:[^<]*)<\/(?:\2)>)/' => static::MATCH_INDENT_NO,
                // DOCTYPE
                '/^<!([^>]*)>/' => static::MATCH_INDENT_NO,
                // tag with implied closing
                '/^<(input|link|meta|base|br|img|source|hr)([^>]*)>/' => static::MATCH_INDENT_NO,
                // self closing SVG tags
                '/^<(animate|stop|path|circle|line|polyline|rect|use)([^>]*)\/>/' => static::MATCH_INDENT_NO,
                // opening tag
                '/^<[^\/]([^>]*)>/' => static::MATCH_INDENT_INCREASE,
                // closing tag
                '/^<\/([^>]*)>/' => static::MATCH_INDENT_DECREASE,
                // self-closing tag
                '/^<(.+)\/>/' => static::MATCH_INDENT_DECREASE,
                // whitespace
                '/^(\s+)/' => static::MATCH_DISCARD,
                // text node
                '/([^<]+)/' => static::MATCH_INDENT_NO
            );
            $rules = array('NO', 'DECREASE', 'INCREASE', 'DISCARD');

            foreach ($patterns as $pattern => $rule) {
                if ($match = preg_match($pattern, $subject ?? '', $matches)) {
                    $this->log[] = array(
                        'rule' => $rules[$rule],
                        'pattern' => $pattern,
                        'subject' => $subject,
                        'match' => $matches[0]
                    );

                    $subject = mb_substr($subject ?? '', mb_strlen($matches[0]));

                    if ($rule === static::MATCH_DISCARD) {
                        break;
                    }

                    if ($rule === static::MATCH_INDENT_NO) {
                    } elseif ($rule === static::MATCH_INDENT_DECREASE) {
                        $nextLineIndentationLevel--;
                        $indentationLevel--;
                    } else {
                        $nextLineIndentationLevel++;
                    }

                    if ($indentationLevel < 0) {
                        $indentationLevel = 0;
                    }

                    $output .= str_repeat($this->options['indentation_character'], $indentationLevel) . $matches[0] . "\n";

                    break;
                }
            }
        } while ($match);

        $interpretedInput = '';
        foreach ($this->log as $e) {
            $interpretedInput .= $e['match'];
        }

        if ($interpretedInput !== $input) {
            throw new \RuntimeException('Did not reproduce the exact input.');
        }

        $output = preg_replace('/(<(\w+)[^>]*>)\s*(<\/\2>)/u', '\\1\\3', $output);

        foreach ($this->temporaryReplacementsScript as $i => $original) {
            $output = str_replace('<script>' . ($i + 1) . '</script>', $original, $output ?? '');
        }

        foreach ($this->temporaryReplacementsInline as $replacement) {
            foreach ($replacement as $i => $original) {
                $output = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $output ?? '');
            }
        }

        return trim($output ?? '');
    }

    /**
     * Debugging utility. Get log for the last indent operation.
     *
     * @return array<mixed>
     */
    public function getLog()
    {
        return $this->log;
    }
}

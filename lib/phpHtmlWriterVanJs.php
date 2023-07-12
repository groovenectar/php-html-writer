<?php

/**
 * an HTML element composed with a tag, html attributes and some content
 * can be rendered using __toString() or by simple string concatenation:
 * 'some text'.$element
 */
class phpHtmlWriterVanJs
{
	/**
	 * the HTML Tag (like "div" or "a")
	 * @var string
	 */
	protected $tag;

	/**
	 * the HTML attributes
	 * @var string
	 */
	protected $attributes;

	/**
	 * the tag content
	 * @var mixed
	 */
	protected $content;

	/**
	 * the character encoding
	 * @var string
	 */
	protected $encoding;

	/**
	 * is a self-closing element
	 * @var bool
	 */
	protected $isSelfClosing;

	/**
	 * from the W3 Schools reference site: http://www.w3schools.com/tags/ref_byfunc.asp
	 * @var array
	 */
	protected static $selfClosingTags = array('area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta');

	public function __construct($tag, array $attributes = array(), $content = '', $encoding = 'UTF-8')
	{
		$this->tag            = $tag;
		$this->attributes     = $attributes;
		$this->content        = $content;
		$this->encoding       = $encoding;
		$this->isSelfClosing  = in_array($this->tag , self::$selfClosingTags);

		if(empty($this->tag))
		{
			throw new InvalidArgumentException('You must provide an HTML tag');
		}

		if($this->isSelfClosing() && !empty($this->content))
		{
			throw new InvalidArgumentException($this->tag.' is a self-closing element, and does not support content');
		}
	}

	/**
	 * Render the element
	 * @return  string  valid XHTML representation of the element
	 */
	public function render()
	{
		$tag        = $this->getTag();
		$attributes = $this->getAttributesAsString();
		$content    = $this->getContent();
		$html = $tag . '(';
		if (count($this->getAttributes())) {
			$html .= $attributes;
			if (!empty($content)) {
				$html .= ', ';
			}
		}
		if (!empty($content)) {
			if (is_string($content)) {
				$html .= json_encode($content);
			} else {
				$html .= (string)$content;
			}
		}
		return $html . ')';
	}

	/**
	 * Tell if the tag if self-closing
	 * @return  bool  return true if the tag is self-closing, false otherwise
	 */
	public function isSelfClosing()
	{
		return $this->isSelfClosing;
	}

	/**
	 * Get the element tag name
	 * @return  string  HTML tag name
	 */
	public function getTag()
	{
		return $this->tag;
	}

	/**
	 * Get the element tag attributes
	 * @return  array  HTML tag attributes
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Get the element content
	 * @return  string  HTML tag content
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Get the character encoding
	 * @return  string  the current character encoding
	 */
	public function getEncoding()
	{
		return $this->encoding;
	}

	/**
	 * Set the character encoding
	 * @param  string  $encoding  the new character encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 * Get a HTML valid string containing the element attributes
	 * @return  string  HTML representation of the element attributes
	 */
	public function getAttributesAsString()
	{
		if (empty($this->getAttributes())) {
			return '';
		}
		$collection = [];
		foreach ($this->getAttributes() as $attribute => $value) {
			$key = preg_match('/^[a-zA-Z_][a-zA-Z_0-9]+$/', $attribute) ? $attribute : '"' . $attribute . '"';
			$collection[] = $key . ': ' . json_encode($value);
		}
		return '{' . implode(', ', $collection) . '}';
	}

	/**
	 * @see phpHtmlWriterElement::render()
	 */
	public function __toString()
	{
		return $this->render();
	}
}
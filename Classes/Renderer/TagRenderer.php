<?php

namespace Sethorax\Assetsloader\Renderer;

class TagRenderer
{
    /**
     * @var \DOMDocument
     */
    protected $doc;

    /**
     * @var \DOMElement
     */
    protected $tag;

    /**
     * Creates the DOMElement.
     *
     * @param string $tagName
     * @return $this
     */
    public function create($tagName)
    {
        $this->doc = new \DOMDocument();
        $this->tag = $this->doc->createElement($tagName);

        return $this;
    }

    /**
     * Adds an attribute to the element.
     *
     * @param string $attributeName
     * @param string $attributeValue
     * @return $this
     */
    public function addAttribute($attributeName, $attributeValue = '')
    {
        $this->tag->setAttribute($attributeName, $attributeValue);

        return $this;
    }

    /**
     * Sets the element content.
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->tag->nodeValue = $content;

        return $this;
    }

    /**
     * Renders the DOMElement as a string.
     *
     * @return string
     */
    public function renderToString()
    {
        $this->doc->appendChild($this->tag);
        return $this->doc->saveHTML();
    }
}

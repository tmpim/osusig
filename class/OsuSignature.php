<?php
/**
 * The osu! signature object.
 *
 * @author Lemmmy
 */
class OsuSignature extends Signature
{
	/**
	 * The user data of the requested signature's subject
	 *
	 * @var array
	 */
	private $user;

	/**
	 * The width of the signature excluding the margin and stroke width
	 * calculated by the size of all components.
	 *
	 * @var int
	 */
	private $baseWidth;

	/**
	 * The height of the signature excluding the margin and stroke width
	 * calculated by the size of all components.
	 *
	 * @var int
	 */
	private $baseHeight;

	/**
	 * The template this signature will use for its components.
	 *
	 * @var Template
	 */
	private $template;

	/**
	 * The hex colour of the signature.
	 *
	 * @var string
	 */
	private $hexColour;

	/**
	 * Creates a new osu! signature.
	 *
	 * @param array $user The user whom the signature will be the signature's subject
	 * @param Template $template The template this signature will be based on.
	 */
	public function __construct($user, $template) {
		$this->user = $user;
		$this->template = new $template($this);

		$width = $this->template->calculateBaseWidth() + ($this->template->getImageMarginWidth());
		$height = $this->template->calculateBaseHeight() + ($this->template->getImageMarginWidth());

		parent::__construct($width, $height);
	}

	/**
	 * @return array The user data of the requested signature's subject
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getHexColour()
	{
		return $this->hexColour;
	}

	public function generate($hexColour = "#bb1177") {
		/*
			The inner width and height of the signature card.
			This excludes the margins.
		*/
		$this->baseWidth = $this->template->calculateBaseWidth();
		$this->baseHeight = $this->template->calculateBaseHeight();

		$this->hexColour = $hexColour;

		$this->template->getCard()->draw($this->canvas, $hexColour, $this->template, $this->baseWidth, $this->baseHeight);
		$this->template->drawComponents();

		// Sets the headers and echoes the image
		$this->output();
	}
}
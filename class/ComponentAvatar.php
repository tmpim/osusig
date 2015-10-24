<?php
/**
 * @author Lemmy
 */
class ComponentAvatar extends Component
{
    /**
     *  The margin between the card content and the avatar
     *
     * @var int
     */
    private $margin;

    /**
     * The memcache object
     *
     * @var Memcached
     */
    private $mc;

    public function __construct($x = 0, $y = 0) {
        $this->margin = isset($_GET['removeavmargin']) ? 0 : 2;

        $this->mc = new Memcached();
        $this->mc->addServer("localhost", 11211);

        parent::__construct($x, $y);
    }

    public function getWidth() {
        return 80;
    }

    public function getHeight() {
        return 80;
    }

    public function draw(Signature $signature)
    {
        parent::draw($signature);


    }
}
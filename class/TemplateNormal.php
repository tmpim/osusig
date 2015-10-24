<?php
/**
 * @author Lemmy
 */
class TemplateNormal extends Template
{
    /**
     * Add the components.
     */
    public function __construct() {
        $this->addComponent(new ComponentAvatar(7, 7));
    }
}
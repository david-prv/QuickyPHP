<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class MethodNode
 */
class MethodNode {
    /**
     * Data
     *
     * @var string
     */
    public string $data;

    /**
     * Left child node
     *
     * @var MethodNode|null
     */
    public ?MethodNode $left;

    /**
     * Right child node
     *
     * @var MethodNode|null
     */
    public ?MethodNode $right;

    /**
     * MethodNode constructor.
     *
     * @param string $data
     */
    public function __construct(string $data) {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
    }
}
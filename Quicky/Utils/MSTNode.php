<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

namespace Quicky\Utils;

/**
 * Class MSTNode
 */
class MSTNode
{
    /**
     * Data
     *
     * @var string
     */
    public string $data;

    /**
     * Left child node
     *
     * @var MSTNode|null
     */
    public ?MSTNode $left;

    /**
     * Right child node
     *
     * @var MSTNode|null
     */
    public ?MSTNode $right;

    /**
     * MSTNode constructor.
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
    }
}

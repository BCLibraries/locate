<?php

namespace App\Service\MapRewriter;

/**
 * Class SvgNamespaces
 *
 * Convenient central storage for namespaces used in SVGs.
 *
 * @package App\Service\MapRewriter
 */
class SvgNamespaces
{
    public const XLINK_ALIAS = 'xlink';
    public const SVG_ALIAS = 'svg';

    public const XLINK_URI = 'http://www.w3.org/1999/xlink';
    public const SVG_URI = 'http://www.w3.org/2000/svg';

    public const NAMESPACES = [
        'xlink' => 'http://www.w3.org/1999/xlink',
        'svg' => 'http://www.w3.org/2000/svg'
    ];
}
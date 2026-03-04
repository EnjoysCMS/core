<?php

namespace EnjoysCMS\Core\Routing\Annotation;

/**
 * @deprecated remove in core 7.x
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Route extends \EnjoysCMS\Core\Routing\Attribute\Route {}

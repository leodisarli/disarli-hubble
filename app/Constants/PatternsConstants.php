<?php

namespace App\Constants;

class PatternsConstants
{
    const FILTER = '/^(nul|nnu)|^(eql|neq|gt|lt|gte|lte|lik)[,]([0-9a-zA-Z-:.@+-_\s]+)$/';
    const UUID = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/';
    const ULID = '/[0-9A-Z]{26}/';
}

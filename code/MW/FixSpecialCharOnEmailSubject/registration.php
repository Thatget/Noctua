<?php

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'MW_FixSpecialCharOnEmailSubject',
    __DIR__
);
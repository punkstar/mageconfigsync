<?php

namespace MageConfigSync\Api;

interface InstallationDetectorInterface
{
    /**
     * @return bool
     */
    public function isInstallationDetected();
}
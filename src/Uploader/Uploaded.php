<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

interface Uploaded
{
    /**
     * @return string
     */
    public function getContentFieldName();

    /**
     * @return string
     */
    public function getPath();
}

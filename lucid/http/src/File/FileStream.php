<?php

namespace Lucid\Http\Request;

use Lucid\Http\Traits\StreamedBody;

class FileStream implements StreamInterface
{
    use StreamedBody;

    public function __construct(FileInfo $info)
    {
        $this->setResource(fopen($info->name));
    }
}

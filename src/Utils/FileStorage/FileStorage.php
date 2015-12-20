<?php

namespace LizardsAndPumpkins\Utils\FileStorage;

interface FileStorage
{
    /**
     * @param StorageAgnosticFileUri $identifier
     * @return File
     */
    public function file(StorageAgnosticFileUri $identifier);

    /**
     * @param StorageAgnosticFileUri $identifier
     * @return bool
     */
    public function contains(StorageAgnosticFileUri $identifier);

    /**
     * @param StorageAgnosticFileUri $identifier
     * @param FileContent $content
     */
    public function putContent(StorageAgnosticFileUri $identifier, FileContent $content);

    /**
     * @param StorageAgnosticFileUri $identifier
     * @return FileContent
     */
    public function getContent(StorageAgnosticFileUri $identifier);
}

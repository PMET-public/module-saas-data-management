<?php

namespace MagentoEse\SaasDataManagement\Api;

use MagentoEse\SaasDataManagement\Api\Data\DataSpaceInterface;

interface DataSpaceRepositoryInterface
{
    
    /**
     * Save DataSpace Configuration
     *
     * @param DataSpaceInterface $dataSpace
     * @return mixed
     */
    public function save(DataSpaceInterface $dataSpace);

    /**
     * Get DataSpace Configuration
     *
     * @return DataSpaceInterface
     */
    public function get();
}
